eZMigrationBundle2 deprecations and backwards compatibility breaks
==================================================================

(intro...)

* the default migration directory name is now `MigrationsDefinitons`, instead of `MigrationVersions`.
  A Symfony parameter is available to tweak that name if you feel the need to.
  Also, the `src/MigrationsDefinitons` directory is searched for migrations, if it exists, besides the
  bundles directories.

* config parameter `ez_migration_bundle.version_directory` was renamed to `ez_migration_bundle.version_directory`

* service `ez_migration_bundle.complex_field.ezpage` has been removed, as upstream has dropped the ezpage field type

* cli command `kaliop:migration:update` was removed. It was an alias of `kaliop:migration:migrate`

* removed the following deprecated php classes and interfaces:
  - interface `API\LanguageAwareInterface`
  - class `Core\ComplexField\AbstractComplexField`
  - class `Core\ComplexField\ComplexFieldManager`
  - class `Core\Matcher\ContentMatcher\ContentResolver`
  - class `Core\ProcessBuilder`
  - class `Core\StorageHandler\Database`

* removed support for the following deprecated elements of migrations definitions:
  - content/create: main_location
  - content/create: other_locations
  - lang/delete: lang
  - lang/update: lang
  - ezmedia fieldtype definition: fileName, mimeType, inputUri
  - match by: contenttype_id, contenttype_identifier, contenttypegroup_id, contenttypegroup_identifier,
    objectstate_id, objectstate_identifier, usergroup_id

* the migration steps `trash/delete`, `trash/load` and `trash/recover`
