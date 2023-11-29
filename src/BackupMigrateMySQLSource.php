<?php

namespace Drupal\backup_migrate_mysql;

use Drupal\backup_migrate\Core\Source\MySQLiSource;

/**
 * Class BackupMigrateMySQLSource. Provides wrapper class for the MySQL source plugin.
 */
class BackupMigrateMySQLSource extends MySQLiSource {

  /**
   * {@inheritdoc}
   */
  protected function _getTableCreateSql(array $table) {
    $out = parent::_getTableCreateSql($table);

    $convert = $this->confGet('convert') ?? '';
    switch ($convert) {
      case 'mariadb':
        $search = ['DEFAULT CHARSET=utf8mb4_0900_ai_ci', 'COLLATE=utf8mb4_0900_ai_ci', 'COLLATE utf8mb4_0900_ai_ci'];
        $replace = ['DEFAULT CHARSET=utf8mb4_unicode_ci', 'COLLATE=utf8mb4_unicode_ci', 'COLLATE utf8mb4_unicode_ci'];
        $out = str_replace($search, $replace, $out);
        break;

      case 'mysql':
        $search = ['DEFAULT CHARSET=utf8mb4_unicode_ci', 'COLLATE=utf8mb4_unicode_ci', 'COLLATE utf8mb4_unicode_ci'];
        $replace = ['DEFAULT CHARSET=utf8mb4_0900_ai_ci', 'COLLATE=utf8mb4_0900_ai_ci', 'COLLATE utf8mb4_0900_ai_ci'];
        $out = str_replace($search, $replace, $out);
        break;

    }

    return $out;
  }

  /**
   * {@inheritdoc}
   */
  public function configSchema(array $params = []) {
    $schema = parent::configSchema($params);

    $schema['fields']['convert'] = [
      'type' => 'enum',
      'title' => $this->t('Convert charset'),
      'options' => [
        'mysql' => $this->t('Convert for MySQL'),
        'mariadb' => $this->t('Convert for MariaDB'),
      ],
    ];

    return $schema;
  }

}
