CREATE TABLE `admin` (
  `adminId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `adminName` varchar(100) NOT NULL,
  `adminPhone` varchar(15),
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `customer` (
  `customerId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `customerName` varchar(100) NOT NULL,
  `customerPhone` varchar(20) NOT NULL UNIQUE,
  `customerEmail` varchar(150) UNIQUE,
  `passwordHash` varchar(255),
  `birthDate` date,
  `gender` ENUM('MALE', 'FEMALE', 'OTHER') DEFAULT 'MALE',
  `address` varchar(255),
  `visitCount` int(11) DEFAULT 0,
  `voucherCount` int(11) DEFAULT 0,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `category` (
  `categoryId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `categoryName` varchar(100) NOT NULL,
  `description` varchar(500),
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `food` (
  `foodId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `categoryId` int(11) NOT NULL,
  `foodName` varchar(150) NOT NULL,
  `foodDescription` varchar(500),
  `foodPrice` decimal(12,0) NOT NULL,
  `foodDiscount` decimal(5,2) NOT NULL DEFAULT 0,
  `foodStatus` varchar(20) NOT NULL DEFAULT 'AVAILABLE',
  `isFeatured` tinyint(1) NOT NULL DEFAULT 0,
  `imageURL` varchar(255),
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `ingredient` (
  `ingredientId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `ingredientName` varchar(150) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `quantityInStock` decimal(12,3) NOT NULL DEFAULT 0,
  `minQuantity` decimal(12,3) NOT NULL DEFAULT 0,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `inventory_log` (
  `logId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `ingredientId` int(11) NOT NULL,
  `adminId` int(11),
  `logType` varchar(20) NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `note` varchar(255),
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `promotion` (
  `promotionId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `badgeText` varchar(50),
  `description` text,
  `expiryInfo` varchar(100),
  `targetAudience` varchar(100),
  `imageURL` varchar(255),
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `voucher` (
  `voucherId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `voucherCode` varchar(50) NOT NULL UNIQUE,
  `discountType` varchar(10) NOT NULL DEFAULT 'PERCENT',
  `discountValue` decimal(12,2) NOT NULL,
  `minOrderValue` decimal(12,0) NOT NULL DEFAULT 0,
  `expiryDate` date,
  `usageLimit` int(11) NOT NULL DEFAULT 1,
  `usedCount` int(11) NOT NULL DEFAULT 0,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `service_table` (
  `tableId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `tableNumber` varchar(10) NOT NULL,
  `area` varchar(50),
  `capacity` int(11) NOT NULL DEFAULT 4,
  `status` varchar(20) NOT NULL DEFAULT 'AVAILABLE',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `reservation` (
  `reservationId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `customerId` int(11),
  `tableId` int(11),
  `guestName` varchar(100) NOT NULL,
  `guestPhone` varchar(20) NOT NULL,
  `guestEmail` varchar(150),
  `reservationDate` date NOT NULL,
  `reservationTime` time NOT NULL,
  `guestCount` int(11) NOT NULL DEFAULT 1,
  `branch` varchar(100),
  `status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `note` text,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `orders` (
  `orderId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `customerId` int(11),
  `adminId` int(11),
  `tableId` int(11),
  `reservationId` int(11),
  `voucherId` int(11),
  `guestCount` int(11),
  `subtotal` decimal(12,0) NOT NULL DEFAULT 0,
  `discountAmount` decimal(12,0) NOT NULL DEFAULT 0,
  `totalAmount` decimal(12,0) NOT NULL DEFAULT 0,
  `paymentMethod` varchar(20),
  `status` varchar(20) NOT NULL DEFAULT 'OPEN',
  `orderDate` datetime NOT NULL DEFAULT (current_timestamp()),
  `paidAt` datetime,
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `order_items` (
  `orderItemId` int(11) PRIMARY KEY AUTO_INCREMENT,
  `orderId` int(11) NOT NULL,
  `foodId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unitPrice` decimal(12,0) NOT NULL,
  `lineTotal` decimal(12,0) NOT NULL,
  `itemStatus` varchar(20) NOT NULL DEFAULT 'ORDERED',
  `note` varchar(255),
  `createdAt` datetime NOT NULL DEFAULT (current_timestamp())
);

-- Bổ sung Khóa ngoại
ALTER TABLE `food` ADD CONSTRAINT `fk_food_category` FOREIGN KEY (`categoryId`) REFERENCES `category` (`categoryId`) ON DELETE RESTRICT;
ALTER TABLE `inventory_log` ADD CONSTRAINT `fk_log_admin` FOREIGN KEY (`adminId`) REFERENCES `admin` (`adminId`);
ALTER TABLE `inventory_log` ADD CONSTRAINT `fk_log_ingredient` FOREIGN KEY (`ingredientId`) REFERENCES `ingredient` (`ingredientId`);
ALTER TABLE `reservation` ADD CONSTRAINT `fk_res_customer` FOREIGN KEY (`customerId`) REFERENCES `customer` (`customerId`) ON DELETE SET NULL;
ALTER TABLE `reservation` ADD CONSTRAINT `fk_res_table` FOREIGN KEY (`tableId`) REFERENCES `service_table` (`tableId`) ON DELETE SET NULL;
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_admin` FOREIGN KEY (`adminId`) REFERENCES `admin` (`adminId`);
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customerId`) REFERENCES `customer` (`customerId`) ON DELETE SET NULL;
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_reservation` FOREIGN KEY (`reservationId`) REFERENCES `reservation` (`reservationId`);
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_table` FOREIGN KEY (`tableId`) REFERENCES `service_table` (`tableId`);
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_voucher` FOREIGN KEY (`voucherId`) REFERENCES `voucher` (`voucherId`);
ALTER TABLE `order_items` ADD CONSTRAINT `fk_oi_food` FOREIGN KEY (`foodId`) REFERENCES `food` (`foodId`);
ALTER TABLE `order_items` ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`orderId`) REFERENCES `orders` (`orderId`) ON DELETE CASCADE;
