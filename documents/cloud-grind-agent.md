# Cloud agent brief — osCommerce test suite (autonomous)

Paste this entire file into a **cloud** Task (`environment: cloud`, `cloud_base_branch: remote-test-*`). The agent must **not** stop after a phase or a green slice to ask whether to continue.

---

## Authority

- **Spec:** `documents/test-suite-plan.md` (decisions in §8 are fixed).
- **Branch:** `remote-test-YYYY-MM-DD` on `Training-Datasmith/oscommerce` (already pushed). Push all fixes to **this branch only**; no force-push; preserve LF; corpus curation only (no deleting tests to pass).

## Execution policy (mandatory)

1. **Run until finished** per [Finished](#finished) below — not until “Phase 0 done” or “first green JUnit.”
2. **Never ask the user** if you should continue, start the next phase, or resume after a milestone. Assume **yes** for every phase in [Work queue](#work-queue).
3. **If a session or turn limit approaches:** commit + push current progress, append a short checklist to `documents/cloud-grind-status.md` (what’s done / next step), then **keep working** in the same run if possible; only end when the platform stops you, then the **next** cloud resume must read `cloud-grind-status.md` and continue without asking.
4. **Fix loop:** for each failing scope, minimal diffs → re-run same scope until **0 failures, 0 errors** (skips OK if documented). JUnit with failures/errors is not completion.
5. **Do not** return a summary that only lists “recommended next steps” while work remains in the queue.

## Environment

- Use `.cursor/install.sh` (create it in W0 if missing): PHP 8.4, `pdo_mysql`, `mbstring`, `xml`, `gd`, **MySQL**, **Apache**, `composer install` when `composer.json` exists.
- Integration canonical on this Linux VM; unit tests must also pass here before you claim M0/M1.

## Work queue (in order — complete all unless [Blocked](#blocked))

| ID | Work | Done when |
|----|------|-----------|
| W0 | `.cursor/environment.json` + `.cursor/install.sh` (LAMP + composer) | `install.sh` succeeds; `php -m` shows required extensions; MySQL and Apache running |
| W1 | Phase 0: dev `composer.json`, `phpunit.xml.dist`, `tests/bootstrap-unit.php`, port `HTMLTest` | `vendor/bin/phpunit --testsuite unit` → **0F/0E** |
| W2 | Phase 1: grow unit tests toward **100% line coverage** of `osCommerce/OM/` | Coverage report; close gaps iteratively; exclusions only with comment in `phpunit.xml.dist` + one line in `documents/coverage-exclusions.md` |
| W3 | `tools/setup-install-harness.php` (or `tests/tools/…`): drive **Setup** install + **full** `oscommerce_sample_data.sql` | Harness exits 0 on fresh DB |
| W4 | Integration tests: `@group integration`, split `@group apache` vs CLI as needed | `vendor/bin/phpunit --testsuite integration` → **0F/0E** |
| W5 | Artifacts: `phpunit-remote-YYYYMMDD.xml` at repo root (or `build/`), optional coverage XML/HTML under `build/coverage/` | Files committed or noted in status for host to `git show` |
| W6 | README snippet in `documents/test-suite-plan.md` or `tests/README.md`: exact commands for unit vs integration | Host can run unit without LAMP |

Update `documents/cloud-grind-status.md` after each W-row completes (checkbox + date + commit SHA).

## Finished

Stop **only** when **all** are true:

- W0–W6 complete.
- **M4** from test-suite-plan §7: **100% line coverage** of `osCommerce/OM/` (minus documented exclusions).
- Full suite: unit **0F/0E** and integration **0F/0E** on PHP 8.4 Linux.
- Latest commit pushed to `remote-test-*`.

Report: test counts, skips, coverage %, PHP/extensions, harness URL used, branch SHA.

## Blocked

Stop early **only** if a hard external blocker remains after reasonable effort (document in `documents/cloud-grind-status.md`):

- Required extension unavailable on cloud image and no apt package.
- Upstream bug with no minimal corpus fix.
- Setup wizard cannot be automated and HTTP trace proves no workaround.

**Not** blocked: large test count, long runtime, “should split into phases,” or needing many commits.

## Parent agent (host) — do not micro-manage

- Launch **one** cloud Task with this brief + `run_in_background: true`.
- Do **not** send follow-up “continue?” messages; use **Task resume** only if the platform ends the run before [Finished](#finished).
- Record baselines from remote JUnit after Finished (`reports/oscommerce/`, `test-baselines.tsv`) when the user asks.
