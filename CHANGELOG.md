# Login link Changelog

## 4.0.1 - 2026-11-01
### Added
- Ability to send login links via a do tag
{% set details = {
    emailAddress: email,
    configUid: configUid
} %}
{% do craft.loginlink.sendLoginLink( details ) %}

## 4.0.0 - 2026-10-16
## Changed
- Upgraded for Craft 4  

### Added
- Added the ability to have different configurations

## 1.0.0 - 2022-09-03
### Added
- Initial release
