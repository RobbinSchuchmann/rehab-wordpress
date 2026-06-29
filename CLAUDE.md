# Rehab Websites — repo & workflow guide

> **On session start, read `PROJECT-STATUS.md`** (same folder) for the current state of play: Linear/git setup progress, the issues to create, what's already built, and the next actions. Session task lists and chat history do not survive restarts; that file does.

## Repository orientation

This directory (`dev-stack/`) is the **git root** (remote: `RobbinSchuchmann/rehab-wordpress`). Launch Claude Code from here so git, `gh`, `.mcp.json` and this file all share one working directory.

- `wp-content-dev/` — the WordPress payload that the Docker stack mounts:
  - `plugins/rehab-blocks/` — the custom Gutenberg block library (`src/` is source, `build/` is the compiled output the site loads; both are tracked). Build with `npm run build` inside that folder.
  - `mu-plugins/` — `aa-block-builders.php` (PHP helpers that emit block markup), `zz-oneshot.php` (named maintenance/rebuild tasks, hit via `?rehab_oneshot=<task>`), `aa-treatment-v3-specs.php` (per-page content specs).
  - `themes/rehab-parent/` — shared parent theme; `*-child/` — per-brand child themes (token overrides).
- The stack runs at `http://localhost:8081` (and `http://5.223.87.211:8081` externally). After a page rebuild, run `node tools/recover-demo.js <id>` then `node tools/check-editor.js <id>` (expect 0 "Attempt recovery").
- The SQL dump + uploads live in the sibling `../diamond-rehab-wordpress-folder/` and are **not** in this repo.

## Linear workflow protocol

Issues and branches are linked by an embedded Linear ID; **git events move status automatically — never set Linear status by hand.** If status looks wrong, the fix is a git event, not a manual change.

- **Team:** Rehab Websites · **identifier `REH`** (branches read `reh-42-short-description`).
- **Assignee:** every issue is assigned to **Robbin Schuchmann**.
- **Status lifecycle (all driven by git):** first push of the branch → *In Progress*; PR opened → *In Review*; PR merged → *Done*. A purely local branch is invisible to Linear; status only moves on first push.

### Trigger: "I want to work on X"

1. **Create the Linear issue first** (via the `linear-server` MCP). Short imperative title; assign to Robbin Schuchmann; infer type label and priority from context; report the ID back (e.g. `REH-42`).
2. **Branch with the ID in the name:** `git checkout -b reh-42-short-description`. Issue before branch, always — the branch name needs the ID. **No ID in the branch name = no link = no automation.**
3. **Work and commit.** Optionally add `fixes REH-42` to a commit message to reinforce the link.
4. **Push → PR → merge:** `git push -u origin <branch>`, then `gh pr create`, then `gh pr merge`. Status marches on its own.

### Conventions

- **Type labels:** Bug / Feature / Improvement. **Default priority:** Medium.
- **Projects** are outcomes that close (e.g. "v3 design rollout"), never open-ended buckets.
- **Scope:** Linear tracks **code work only**. Business/content tasks (copy approvals, asset gathering, the editor go-live gates) live elsewhere.
- **No worktrees** — this repo runs a single tmux session, so work directly on branches.

### Notes

- The Linear MCP can create issues, projects and labels, but **cannot create or rename teams** — that's manual in Linear settings.
- `gh` reads its token from the gitignored `.mcp.env` (see `.mcp.env.example`); the `linear-server` MCP authenticates via OAuth on first use.
