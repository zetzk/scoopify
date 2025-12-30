CREATE TABLE scoopify_session_participants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL,
    user_key VARCHAR(100) NOT NULL,
    user_name VARCHAR(150) NOT NULL,
    session_id BIGINT UNSIGNED NOT NULL,
    estimative int NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_scoopify_session_participants_uuid (uuid),
    INDEX idx_session_id (session_id),
    INDEX idx_user_key (user_key)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;