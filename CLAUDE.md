# dcLatestVersions

Notes for AI assistants working on the Dotclear plugin dcLatestVersions.

**Read first** the shared conventions of the author's Dotclear modules:
https://raw.githubusercontent.com/JcDenis/gh2da/master/CONVENTIONS.md
(if a web tool refuses the address, use `curl -s` on it from the shell).
The rules below take precedence for this module.

## Module

Simply show visitors last versions of nightly build of Dotclear.

No admin page. Public side: behaviors.

Required Dotclear and PHP versions: see `_define.php`.

## Structure

* `src/Backend.php`: admin side: menu entry, favorites, behaviors registration.
* `src/Frontend.php`: public side: behaviors, template tags and URL handlers registration.
* `src/Install.php`: install and update, default settings.
* `src/My.php`: module helpers (`My::id()`, settings, permissions).
* `src/Uninstall.php`: cleaners for the plugin Uninstaller.
* `src/Versions.php`: latest versions helper, with a retry delay when the server is unreachable.
* `src/Widgets.php`: widgets definition and rendering.
* Behaviors used: `initWidgets`, `adminDashboardItemsV2`, `adminDashboardOptionsFormV2`, `adminAfterDashboardOptionsUpdate`.

## Pitfalls

* None recorded yet: add here the traps met while working on the module.

## Testing

* CI browses the common admin and public pages; extra pages can be listed in `.github/dotclear-test.txt`.
