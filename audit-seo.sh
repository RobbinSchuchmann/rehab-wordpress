#!/usr/bin/env bash
# RankMath coverage sweep: fetch every page in the sitemap and confirm each
# emits SEO meta + a RankMath schema block. Flags pages missing any signal.
# Usage: ./audit-seo.sh   (reads /tmp/all-page-urls.txt, writes /tmp/seo-audit.csv)
set -u
URLS="${1:-/tmp/all-page-urls.txt}"
OUT="${2:-/tmp/seo-audit.csv}"
echo "url,status,title,metadesc,canonical,og,schema,schema_types" > "$OUT"

check() {
  local url="$1"
  local body code
  body=$(curl -sL --max-time 30 -w $'\n%{http_code}' "$url")
  code="${body##*$'\n'}"
  body="${body%$'\n'*}"
  local head; head=$(printf '%s' "$body" | sed -n '1,/<\/head>/p')
  local title=0 desc=0 canon=0 og=0 schema=0 types=0
  printf '%s' "$head" | grep -q '<title>' && title=1
  printf '%s' "$head" | grep -q '<meta name="description"' && desc=1
  printf '%s' "$head" | grep -qi 'rel="canonical"' && canon=1
  printf '%s' "$head" | grep -q 'property="og:title"' && og=1
  printf '%s' "$body" | grep -q 'class="rank-math-schema' && schema=1
  types=$(printf '%s' "$body" | grep -oE '"@type":"[A-Za-z]+"' | sort -u | wc -l | tr -d ' ')
  echo "$url,$code,$title,$desc,$canon,$og,$schema,$types" >> "$OUT"
}
export -f check
export OUT

# Run with limited parallelism to be kind to the dev box.
cat "$URLS" | xargs -P 8 -I {} bash -c 'check "$@"' _ {}

echo "=== summary ==="
total=$(($(wc -l < "$OUT") - 1))
echo "pages checked: $total"
echo "non-200:        $(awk -F, 'NR>1 && $2!=200' "$OUT" | wc -l)"
echo "missing title:  $(awk -F, 'NR>1 && $3==0' "$OUT" | wc -l)"
echo "missing desc:   $(awk -F, 'NR>1 && $4==0' "$OUT" | wc -l)"
echo "missing canon:  $(awk -F, 'NR>1 && $5==0' "$OUT" | wc -l)"
echo "missing og:     $(awk -F, 'NR>1 && $6==0' "$OUT" | wc -l)"
echo "missing schema: $(awk -F, 'NR>1 && $7==0' "$OUT" | wc -l)"
echo
echo "=== pages missing schema or meta (if any) ==="
awk -F, 'NR>1 && ($3==0||$4==0||$7==0)' "$OUT" | head -40
