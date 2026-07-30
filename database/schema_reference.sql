```sql
-- Schema Referensi MySQL untuk DEP Service

CREATE TABLE `companies` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_name` VARCHAR(200) NOT NULL,
  `login_url` VARCHAR(255) NOT NULL,
  `icon_storage_path` VARCHAR(255) NULL,
  `material_storage_path` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `affiliations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `affiliation_code` VARCHAR(20) NOT NULL,
  `affiliation_name` VARCHAR(150) NOT NULL,
  `display_order` INT DEFAULT 0,
  `organization_type` TINYINT COMMENT '1: Main store, 2: FC store',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `idx_company_aff_code` (`company_id`, `affiliation_code`),
  FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `job_id` VARCHAR(20) NOT NULL,
  `job_title` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `employees` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `employee_code` VARCHAR(30) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `kana_name` VARCHAR(100) NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone_number` VARCHAR(30) NULL,
  `password` VARCHAR(255) NOT NULL,
  `password_error_count` INT DEFAULT 0,
  `account_status` VARCHAR(20) DEFAULT 'ACTIVE' COMMENT 'ACTIVE, LOCKED, INACTIVE',
  `account_locked_at` DATETIME NULL,
  `is_sys_admin` TINYINT(1) DEFAULT 0,
  `can_register_employee` TINYINT(1) DEFAULT 0,
  `can_register_course` TINYINT(1) DEFAULT 0,
  `can_setting_attendance` TINYINT(1) DEFAULT 0,
  `authority_effective_range` VARCHAR(20) DEFAULT 'ONLY' COMMENT 'ONLY, BELOW, ALL',
  `authority_effective_affiliation_code` VARCHAR(20) NULL,
  `deleted_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `idx_company_emp_code` (`company_id`, `employee_code`),
  FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `employee_affiliations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `affiliation_code` VARCHAR(20) NOT NULL,
  `job_id` VARCHAR(20) NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `course_categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_code` VARCHAR(20) UNIQUE NOT NULL,
  `category_name` VARCHAR(100) NOT NULL,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `course_category_details` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `detail_code` VARCHAR(20) NOT NULL,
  `detail_name` VARCHAR(100) NOT NULL,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`category_id`) REFERENCES `course_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `courses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_detail_id` BIGINT UNSIGNED NOT NULL,
  `course_name` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `has_retest` TINYINT(1) DEFAULT 0,
  `passing_score` INT DEFAULT 70,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`category_detail_id`) REFERENCES `course_category_details`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `course_materials` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `course_id` BIGINT UNSIGNED NOT NULL,
  `material_type` VARCHAR(20) NOT NULL COMMENT 'YOUTUBE, PDF',
  `title` VARCHAR(200) NOT NULL,
  `content_url_or_path` VARCHAR(255) NOT NULL,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `course_enrollments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `course_id` BIGINT UNSIGNED NOT NULL,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `enrollment_deadline` DATE NOT NULL,
  `status` VARCHAR(20) DEFAULT 'ENROLLED' COMMENT 'ENROLLED, COMPLETED, CANCELLED',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;