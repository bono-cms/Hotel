
/* Rooms */
DROP TABLE IF EXISTS `bono_module_hotel_rooms`;
CREATE TABLE `bono_module_hotel_rooms` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `price` FLOAT NOT NULL,
    `adults` SMALLINT NOT NULL COMMENT 'Max.adult capacity',
    `children` SMALLINT NOT NULL COMMENT 'Max.children capacity'
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

/* Rooms booking */
DROP TABLE IF EXISTS `bono_module_hotel_rooms_booking`;
CREATE TABLE `bono_module_hotel_rooms_booking` (

    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Booking ID',
    `room_id` INT NOT NULL COMMENT 'Attached room ID',
    `datetime` DATETIME NOT NULL COMMENT 'Date and time of booking',
    `client` varchar(255) NOT NULL COMMENT 'Client name',
    `amount` FLOAT NOT NULL COMMENT 'Total amount payed',
    `checkin` DATE COMMENT 'Check-in date',
    `checkout` DATE COMMENT 'Check-out date',
    `status` TINYINT NOT NULL COMMENT 'Booking status constant',
    `token` varchar(32) NOT NULL COMMENT 'Booking token'

    FOREIGN KEY (room_id) REFERENCES bono_module_hotel_rooms(id) ON DELETE CASCADE

) ENGINE = InnoDB DEFAULT CHARSET = UTF8;