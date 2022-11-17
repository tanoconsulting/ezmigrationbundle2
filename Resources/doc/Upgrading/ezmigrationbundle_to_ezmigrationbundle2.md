eZMigrationBundle2 deprecations and backwards compatibility breaks
==================================================================

Upgrade notes for developers coming from an ezmgrationbundle installation.

* the default migration directory name is now `MigrationsDefinitions`, instead of `MigrationVersions`.
  A Symfony parameter is available to tweak that name if you feel the need to.
  Also, the `src/MigrationsDefinitons` directory is searched for migrations, if it exists, besides the
  bundles directories.

* config parameter `kaliop_bundle_migration.version_directory` was renamed to `ez_migration_bundle.version_directory`.

* cli command `kaliop:migration:update` was removed. It was an alias of `kaliop:migration:migrate`

* removed support for the following deprecated elements of migrations definitions:
  - content/create: main_location
  - content/create: other_locations
  - lang/delete: lang
  - lang/update: lang
  - ezmedia fieldtype definition: fileName, mimeType, inputUri
  - match by: contenttype_id, contenttype_identifier, contenttypegroup_id, contenttypegroup_identifier,
    objectstate_id, objectstate_identifier, usergroup_id
  - resolve location ids using the `location: $remote_id` syntax
  - set references to: exception_text

* the migration steps `trash/delete`, `trash/load` and `trash/recover`, due to limitations of the cms api, now accept a
  smaller set of filters to match items to act upon

* services `ez_migration_bundle.reference_resolver.content` and `ez_migration_bundle.reference_resolver.location` now
  are triggered using a different tag. Respectively, `content_by_remote_id` and `location_by_remote_id`

* service `ez_migration_bundle.complex_field.ezpage` has been removed, as upstream has dropped the ezpage field type

* deprecated config parameter `kaliop_bundle_migration.table_name` was dropped

* removed the following deprecated php classes and interfaces:
    - interface `API\LanguageAwareInterface`
    - class `Core\ComplexField\AbstractComplexField`
    - class `Core\ComplexField\ComplexFieldManager`
    - class `Core\Matcher\ContentMatcher\ContentResolver`
    - class `Core\ProcessBuilder`
    - class `Core\StorageHandler\Database`

* classes `Core\Matcher\ContentMatcher` and `Core\Matcher\LocationMatcher` were purged of all the methods which had
  been deprecated for a while
