
/* Rooms */
DROP TABLE IF EXISTS `bono_module_hotel_rooms`;
CREATE TABLE `bono_module_hotel_rooms` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `price` FLOAT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

/* Roooms lozations */
DROP TABLE IF EXISTS `bono_module_hotel_rooms_translations`;
CREATE TABLE `bono_module_hotel_rooms_translations` (
    `id` INT NOT NULL COMMENT 'Room ID',
    `lang_id` INT NOT NULL COMMENT 'Corresponding Language ID',
    `name` varchar(254) NOT NULL COMMENT 'Room name',
    `description` TEXT NOT NULL COMMENT 'Room description',

    FOREIGN KEY (id) REFERENCES bono_module_hotel_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE
    
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;