#!/usr/bin/env python3
"""REH-28 — extract author/reviewer data from the OLD live-site SQL dump.

Dump layout (mariadb-dump, --skip-extended-insert style): the `INSERT INTO
`table` (cols)` keyword is on its own line, followed by ONE tuple per line
(`(...)` ending in `,` or `;`), tab-separated fields, with newlines inside
string values escaped as `\n`. So one physical line == one row.

OOM-safe by construction (this is what crashed the session before):

  * The dump is read **line by line** — never `read()` the whole 90 MB file.
    The longest line here is ~0.85 MB (one fat post_content row); peak memory
    stays in the low single-digit MB.
  * For `wp_posts` we parse fields lazily and keep ONLY post_author / status /
    slug / type. The multi-MB `post_content` column is parsed past and
    discarded, never appended to any list. The earlier version retained every
    row body (incl. post_content) in a list and OOM-killed the box (0 B swap).

Usage:
    python3 reh28-extract-old-authors.py [path-to-dump] [out.json]

Output JSON feeds reh28-author-sync.php (committed, so prod needs no dump).
"""
import json
import os
import sys

DEFAULT_DUMP = '../diamond-rehab-wordpress-folder/xscstqwwnp.sql'

# Old-site user ids we care about: 3,4,5,12 = authors, 10 = reviewer (Dhingra).
WANT = {'3', '4', '5', '10', '12'}
REVIEWER_UID = '10'

# wp_usermeta keys worth carrying across (name parts, bio, role, avatar refs).
META_KEYS = {
    'first_name', 'last_name', 'description', 'nickname',
    'wp_user_avatar', 'simple_local_avatar', 'mbg_author',
    'job_title', 'position', 'user_title', 'mts_user_custom_title',
}

# Column indices (verified against the dump's CREATE TABLE statements).
U_ID, U_LOGIN, U_EMAIL, U_URL, U_DISPLAY = 0, 1, 4, 5, 9
P_AUTHOR, P_STATUS, P_NAME, P_TYPE = 1, 7, 11, 20


_UNESC = {'n': '\n', 't': '\t', 'r': '\r', '0': '\0'}


def parse_row(line):
    """Parse one `(field, field, ...)` tuple line into a list of fields.

    Quote/escape aware; tab/space between fields is skipped; bare NULL -> None.
    Returns None if the line isn't a value tuple. One line == one row, so this
    never accumulates across lines — peak memory is a single field list."""
    i = line.find('(')
    if i < 0:
        return None
    i += 1
    n = len(line)
    fields = []
    while i < n:
        while i < n and line[i] in ' \t':  # skip inter-field whitespace
            i += 1
        if i < n and line[i] == "'":  # quoted string field
            i += 1
            cur = []
            while i < n:
                ch = line[i]
                if ch == '\\' and i + 1 < n:
                    nxt = line[i + 1]
                    cur.append(_UNESC.get(nxt, nxt))
                    i += 2
                    continue
                if ch == "'":
                    i += 1
                    break
                cur.append(ch)
                i += 1
            fields.append(''.join(cur))
        else:  # bare token: number, NULL, up to , or )
            start = i
            while i < n and line[i] not in ',)':
                i += 1
            tok = line[start:i].strip()
            fields.append(None if tok == 'NULL' else tok)
        # consume the separator
        while i < n and line[i] in ' \t':
            i += 1
        if i < n and line[i] == ',':
            i += 1
            continue
        if i < n and line[i] == ')':
            break
    return fields


def main():
    dump = sys.argv[1] if len(sys.argv) > 1 else DEFAULT_DUMP
    out_path = sys.argv[2] if len(sys.argv) > 2 else (
        os.path.join(os.path.dirname(__file__), 'reh28-authors.json'))

    users = {}
    umeta = {uid: {} for uid in WANT}
    slug_author = {}
    authored_count = {uid: 0 for uid in WANT}

    table = None  # which INSERT block we're currently inside
    with open(dump, 'r', encoding='utf-8', errors='replace') as fh:
        for line in fh:
            if line.startswith('INSERT INTO `'):
                # e.g. "INSERT INTO `wp_posts` (`ID`, ...)"
                table = line[13:line.find('`', 13)]
                continue
            if table is None or not line.startswith('('):
                if line.startswith('('):
                    continue  # stray tuple with no table context — skip
                table = None  # any other statement ends the block
                continue

            if table == 'wp_users':
                f = parse_row(line)
                uid = (f[U_ID] or '').strip()
                if uid in WANT:
                    users[uid] = {
                        'login': f[U_LOGIN], 'email': f[U_EMAIL],
                        'url': f[U_URL], 'display': f[U_DISPLAY],
                    }
            elif table == 'wp_usermeta':
                f = parse_row(line)  # (umeta_id, user_id, meta_key, meta_value)
                uid = (f[1] or '').strip()
                if uid in WANT and f[2] in META_KEYS:
                    umeta[uid][f[2]] = f[3]
            elif table == 'wp_posts':
                f = parse_row(line)
                # post_content (f[4]) is parsed past but never stored.
                if len(f) <= P_TYPE:
                    continue
                status, name, ptype = f[P_STATUS], f[P_NAME], f[P_TYPE]
                if name and status == 'publish' and ptype in ('post', 'page'):
                    author = (f[P_AUTHOR] or '').strip()
                    slug_author[name] = author
                    if author in authored_count:
                        authored_count[author] += 1

    out = {
        'reviewer_uid': REVIEWER_UID,
        'users': users,
        'usermeta': umeta,
        'authored_count': authored_count,
        'slug_count': len(slug_author),
        'slug_author': slug_author,
    }
    with open(out_path, 'w', encoding='utf-8') as fh:
        json.dump(out, fh, indent=2, ensure_ascii=False)

    print('users found:', sorted(users))
    print('authored_count (old publish post/page):', authored_count)
    print('total slug→author entries:', len(slug_author))
    print('wrote:', out_path)


if __name__ == '__main__':
    main()
