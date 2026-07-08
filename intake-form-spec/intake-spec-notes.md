# Intake form spec — notes

Canonical spec: `intake-spec.json`, built from `prod-intake.html` (Forminator form **11745** — the file also contains form 47, ignored) for order/layout, and `intake-form-parsed.json` (85 fields) for behaviour. Cross-validated against `intake-controls.json` (101 named controls): **all 85 parsed fields placed, all non-internal controls covered, `unmapped` lists are empty.**

## Steps (7 pages, 6 page-breaks)

| # | Section title | Elements | Contents |
|---|---|---|---|
| 0 | *(no section)* | 3 | name-1, email-1, phone-1 (initial contact) |
| 1 | Personal Information | 12 | address-1, date-1 (DOB), gender/occupation, group-4 "Travel information" (passport no. + expiry), "Important note" html, radio-1 "How did you hear about us?" + conditional text-4 |
| 2 | Mental Health & Safety | 4 | radio-9, radio-10, conditional textarea-5 |
| 3 | Emergency contact | 6 | name-2, phone-2, email-2, radio-2 Relationship + conditional text-5 |
| 4 | Support | 7 | radio-7 + conditional name-4/email-4/phone-4/radio-6/radio-8 |
| 5 | General Medical Information | 22 | radio-5 smoker, 3 textareas, group-1 medications, weight/height + unit selects, group-2 previous treatment, group-3 type of addiction, text-13/14, radio-3 detox, group-5 background |
| 6 | Account and Billing Information | 16 | name-3, phone-3, email-3, invoice/VAT, address-3, radio-4 time frame + conditional text-17, "What does your investment include" html, signature-1, date-3 |

Pagination header is a **progress bar** (`pagination-header: bar`, design `show`) — no step labels are displayed. The rendered `data-label` values are Forminator autolabels (`Page  1`…`Page  6`, double space included) and, oddly, the **first** page carries `data-label="Finish"` (Forminator quirk; cosmetic only since the bar shows no labels). Recorded verbatim in `paginationLabels`. All page-break buttons: `« Previous Step` / `Next Step »`.

## Conditional logic (all `show`, ported 9/9)

| Target field | Rule | Trigger |
|---|---|---|
| text-4 "Please specify" | all | radio-1 is `Other` |
| textarea-5 (suicide-attempt details) | all | radio-10 is `Instagram` (= the **Yes** option, see value warning below) |
| text-5 "Specify" | all | radio-2 is `Other` |
| name-4 / email-4 / phone-4 / radio-6 | any | radio-7 is `Another family member or supporter` |
| radio-8 (permission to contact) | any | radio-7 is `Same as emergency contact` **or** `Another family member or supporter` |
| text-17 "Please specify" | all | radio-4 is `Other` |

**⚠ Option-value warning:** several radios carry legacy junk *values* that do not match their labels — e.g. radio-9/radio-10 options are Yes=`Instagram`, No=`one`, Prefer not to say=`Reddit`; radio-1 Friend=`Instagram`, Google=`one`, Social Media=`two`. Conditions reference these raw values (`radio-10 is "Instagram"` = "Yes"). When rebuilding, either keep the value strings verbatim, or remap values and condition rules **together**. Values in the spec are verbatim from the live form.

## Repeatable groups (from DOM `data-options`)

| Group | Fields | min/max | Add / Remove button |
|---|---|---|---|
| group-1 Current medications | text-7 Medication Name, text-8 Daily Dosage | 1 / unlimited | Add medication / Remove medication |
| group-2 Previous treatment history | text-9 Facility, text-10 Year (one row, 6+6) | 1 / unlimited | Add facility / Remove facility |
| group-3 Type of addiction | text-11 Substance, text-12 Daily consumption (one row, 6+6) | 1 / unlimited | Add a substance / Remove a substance |

Non-repeating layout groups (kind `group`, `repeatable: false`): **group-4 Travel information** (text-3 Passport Number, date-2 Passport Expiry) and **group-5 Background information** (textarea-4, with a long description).

## Signature & dates

- **signature-1** (required): canvas e-signature, PNG output, 180px height, pen thickness 2; the DOM carries a hidden input `field-signature-1` holding the signature prefix ID — recorded as `signature.hiddenControl`.
- All three date fields are **select-type** (day/month/year dropdowns), not calendars: date-1 DOB (`dd/mm/yy`, no default, not required), date-2 Passport Expiry (`dd-mm-yy`, no default), date-3 signature Date (`dd-mm-yy`, **defaults to today**, required, has Day/Month/Year sub-labels).

## Notification routing (1 notification, no conditions)

- **To:** info@diamondrehabthailand.com, thediamondrehabth@gmail.com, sergio@diamondrehabthailand.com
- **BCC:** marketing@diamondrehabthailand.com — **CC / From / Reply-To:** none (plugin defaults)
- **Subject:** `Intake Form {name-1} (#{submission_id})`
- **Body:** "You have a new intake form (Diamond): See attached PDF file. All info also here below `{all_fields}` … `{site_url}`"
- **Attachment:** submission PDF, Forminator PDF template id **11790** (the PDF template itself is not in these inputs — must be rebuilt separately if PDF attachment is kept).

## Submission behaviour

`behaviour-thankyou` (from `behaviors[0]`): message *"Thank you for providing all the necessary information! Your journey has begun!"*, autoclose after 5 s. The behavior also stores an (unused, since behaviour is thank-you) `redirect-url: /schedule-thank-you/`. The older top-level settings thank-you message ("Thank you for contacting us, we will be in touch shortly.") is superseded by the behavior one. Honeypot enabled (`input_59`), AJAX submit, submissions stored, validation on submit + inline.

## Decisions / ambiguities

1. **Name fields render as single inputs.** Parsed meta for name-2/3/4 enables prefix/first/middle/last subfields, but `multiple_name` is not true, so the DOM renders one plain input per name field. Spec follows the DOM (`subfields: null`, `multipleName: false`).
2. **`intake-controls.json` "required" flags are unreliable** — they reflect the mere presence of a `data-required` attribute (often empty). Actual `required` in the spec comes from parsed meta plus DOM `aria-required="true"` / asterisk markers. E.g. text-1/2/3 and address-1 subfields are *not* actually required despite `req` in the controls dump.
3. **Collapse toggles:** 12 html fields whose only content is a `▼` label (html-2..14 except 1/15) are decorations for the site's collapse CSS/JS; recorded as `{"kind":"html","collapseToggle":true}` per instructions, not dropped.
4. **Content html blocks:** html-15 "Important note" (passport validity) and html-1 "What does your investment include:" (full benefits list incl. nested check-up list) are included with sanitised HTML (inline styles, styling-only spans, and empty page-builder `<footer>`s stripped).
5. **Address country lists** are the full ~250-country Forminator list — represented as `optionsSource: "country-list"` instead of inlining; address-1 country defaults to `Australia` (`data-default-value`).
6. **Weight/Height row:** number-1 + select-2 (kgs/lbs, default kgs) + number-2 + select-1 (cm/inches, default cm) share one row, 3 cols each; number limits Weight 10–900, Height 1–300.
7. **Phone fields:** validation `none`; phone-2/3/4 have a Forminator character limit of 10 (recorded, though not enforced client-side as a pattern).
