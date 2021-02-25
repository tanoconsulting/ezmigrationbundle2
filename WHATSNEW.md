Veersion: unreleased
====================

* Fixed: one php exception upon throwing an exception (for bad content_type/create steps)

Version 1.0-alpha2
==================

* Fixed one php warning

* Fixed composer dependencies: we need doctrine/dbal 2.11 or later


Version 1.0-alpha
=================

*Explanation of the 'aplha' tag:*

1. the codebase itself is fairly _stable_ and _complete_, as it is a fork of a project which had well over 100 releases already
2. on the other hand, given that the underlying cms framework has evolved a lot, there might be bugs due to API changes
3. also, not all features of the underlying cms framework are fully supported

*Known bugs and missing features:*

- migration step `language/delete` is currently broken due to upstream bug https://issues.ibexa.co/browse/EZP-32349
- see https://github.com/tanoconsulting/ezmigrationbundle2/issues/4 for missing features

*BC guarantees on the road to 1.0 release:*

- the current yml migration format will be kept stable (possibly parts of it deprecated, though)
- the cli commands api will be kept stable
- the php classes and services will surely be heavily refactored. Please no subclassing / injecting them in your code for now

*BC with eZMigrationBundle:*

See [ezmigrationbundle_to_ezmigrationbundle2.md](Resources/doc/Upgrading/ezmigrationbundle_to_ezmigrationbundle2.md)
for all API changes if you are used to eZMigrationBundle 1.
