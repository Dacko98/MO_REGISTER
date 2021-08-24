#List of tables
show tables;


#NOT NULL DEFAULT ''

/**
 * NO REFERENCES, BASE TABLE
 **/
CREATE TABLE users(
	`id_user`	BIGINT(20) UNSIGNED AUTO_INCREMENT,
	`role_id`	TINYINT UNSIGNED,
	`name`		VARCHAR(64),
	`address`	TEXT,
	`email`		VARCHAR(64),
	`info`		TEXT,
	`birthday`	DATE,
	`password`	VARCHAR(32),
	`remember_token`	VARCHAR(100),
	#`settings`	VARCHAR(100),
	`created_at`	TIMESTAMP,
	`updated_at`	TIMESTAMP,
	`last_seen_at`	TIMESTAMP,
	PRIMARY KEY (`id_user`),
	CONSTRAINT check_email_user CHECK ( REGEXP_LIKE(`email`,'^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,63}$'))
);

/**
 * NO REFERENCES, BASE TABLE
 **/
CREATE TABLE projects(
	`id`	INT(10) UNSIGNED AUTO_INCREMENT,
	`title`	VARCHAR(64) COLLATE utf8mb4_unicode_ci,
	`shortDescription`	VARCHAR(512) COLLATE utf8mb4_unicode_ci,
	`description`	LONGTEXT COLLATE utf8mb4_unicode_ci,
	`volunteers`	BOOL,
	`image`			VARCHAR(255),
	`from`			DATE,
	`to`			DATE,
	`created_at`	TIMESTAMP,
	`updated_at`	TIMESTAMP,
	PRIMARY KEY (`id`)
);

/**
 * NO REFERENCES, BASE TABLE
 **/
CREATE TABLE mos(
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`title` VARCHAR(64) COLLATE utf8mb4_unicode_ci,
	`city` TINYTEXT COLLATE utf8mb4_unicode_ci,
	`region` TINYTEXT COLLATE utf8mb4_unicode_ci,
	`address` TINYTEXT COLLATE utf8mb4_unicode_ci,
	`orientation` TINYTEXT COLLATE utf8mb4_unicode_ci,
	`type` TINYTEXT COLLATE utf8mb4_unicode_ci,
	`shortDescription` VARCHAR(512) COLLATE utf8mb4_unicode_ci,
	`Description` LONGTEXT COLLATE utf8mb4_unicode_ci,
	`profile_image` VARCHAR(64),
	`website` VARCHAR(64),
	`created_at`	TIMESTAMP,
	`updated_at`	TIMESTAMP,
	PRIMARY KEY (`id`)
);

CREATE TABLE posts(
	`id`		INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	`title`		VARCHAR(64) COLLATE utf8mb4_unicode_ci,
	`body`		LONGTEXT COLLATE utf8mb4_unicode_ci,
	`filenames` VARCHAR(255),
	`user_id`	BIGINT(20) UNSIGNED,
	`id_organization`	INT(10) UNSIGNED,
	`id_project`		INT(10) UNSIGNED,
	`created_at`	TIMESTAMP,
	`updated_at`	TIMESTAMP,
	CONSTRAINT posts_userid_fk FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE,
	CONSTRAINT posts_id_organization_fk  FOREIGN KEY (id_organization) REFERENCES mos(id) ON DELETE CASCADE,
	CONSTRAINT posts_id_project_fk  FOREIGN KEY (id_project) REFERENCES mos(id),
);


CREATE TABLE BIND_User_Organization(
	`id_user`			BIGINT UNSIGNED NOT NULL,
	`id_organization`	INT UNSIGNED NOT NULL,
	CONSTRAINT bind_proj_org_userid FOREIGN KEY (id_user) REFERENCES users(id_user),
    CONSTRAINT bind_proj_org_orgid  FOREIGN KEY (id_organization) REFERENCES mos(id)
);

CREATE TABLE BIND_Project_Organization(
	`id_project`		INT UNSIGNED NOT NULL,
	`id_organization`	INT UNSIGNED NOT NULL REFERENCES Organization(id_organization),
    CONSTRAINT bind_proj_org_projid FOREIGN KEY (id_project) REFERENCES projects(id),
    CONSTRAINT bind_proj_org_orgid2  FOREIGN KEY (id_organization) REFERENCES mos(id)
);