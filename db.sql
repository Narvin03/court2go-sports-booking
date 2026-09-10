CREATE DATABASE IF NOT EXISTS assignment_db
  CHARACTER SET utf8 
  COLLATE utf8_general_ci;

USE assignment_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,       
    name VARCHAR(100) NOT NULL,              
    email VARCHAR(100) NOT NULL UNIQUE,     
    password VARCHAR(255) NOT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    birthday DATE NULL,
    bio VARCHAR(255) NULL,
    age INT NULL,
    gender VARCHAR(10) NULL,
    location VARCHAR(100) NULL,
    profile_image LONGBLOB NULL,
    profile_image_type VARCHAR(50) NULL,         
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Password resets table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,       
    email VARCHAR(100) NOT NULL,             
    token VARCHAR(64) NOT NULL,              
    expires DATETIME NOT NULL,               
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Venues table
CREATE TABLE IF NOT EXISTS venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sport_category VARCHAR(50) NOT NULL,
    description TEXT,
    location VARCHAR(150),
    price DECIMAL(10,2) NOT NULL,
    time_availability VARCHAR(100) NOT NULL,
    amenities TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Venue images table
CREATE TABLE IF NOT EXISTS venue_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE
);

-- Promotions table
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    expiry DATE,
    discount_type VARCHAR(20) DEFAULT NULL, -- 'percent' or 'fixed'
    discount_value DECIMAL(5,2) DEFAULT NULL,
    venue_id INT DEFAULT NULL,
    sport_category VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Favorites: user <-> venue (one row means user favorited that venue)
CREATE TABLE IF NOT EXISTS favorites (
  id         INT NOT NULL AUTO_INCREMENT,
  user_id    INT NOT NULL,
  venue_id   INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_user_venue (user_id, venue_id),
  KEY idx_user (user_id),
  KEY idx_venue (venue_id),
  CONSTRAINT fk_fav_user  FOREIGN KEY (user_id)  REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_fav_venue FOREIGN KEY (venue_id) REFERENCES venues(id)
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- Receipt table
CREATE TABLE IF NOT EXISTS receipt (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reference VARCHAR(100) NOT NULL,
    court_id VARCHAR(100) NOT NULL,                                   
    price DECIMAL(10, 2) NOT NULL,
    start TIME NOT NULL,
    end TIME NOT NULL,
    duration INT NOT NULL,
    booking_date DATE NOT NULL,
    receipt_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('booked','completed','cancelled') DEFAULT 'booked',
    FOREIGN KEY (court_id) REFERENCES court(venues_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);


-- Sample data insertion
-- venues data
INSERT INTO venues (name, sport_category, description, location, price, time_availability, amenities) VALUES
('Sunshine Court', 'Badminton', 'Indoor badminton hall with 6 wooden courts and bright LED lighting.', '123 Main Street, Kuala Lumpur', 20.00, '08:00 - 22:00', 'Parking, Locker Rooms, WiFi'),
('Sky Arena', 'Pickleball', 'Spacious pickleball arena with 4 courts and modern facilities.', 'Lot 22, Jalan Bukit Bintang, Kuala Lumpur', 30.00, '07:00 - 21:00', 'Parking, Equipment Rental, Refreshments'),
('Ocean View Tennis Club', 'Tennis', 'Outdoor tennis courts with ocean view and premium seating area.', 'Pantai Dalam, Selangor', 10.00, '06:00 - 20:00', 'Parking, Shower, Coaching Services'),
('Greenfield Stadium', 'Football', 'Large football stadium with grass pitch and floodlights for night matches.', 'Jalan Ampang, Kuala Lumpur', 25.00, '09:00 - 23:00', 'Floodlights, Locker Rooms, Parking'),
('Summit Arena', 'Basketball', 'Indoor basketball arena suitable for tournaments and training.', 'Bukit Jalil, Kuala Lumpur', 15.00, '08:00 - 22:00', 'Air Conditioning, Locker Rooms, Parking'),
('Maple Sports Center', 'Volleyball', 'Community center with indoor volleyball facilities.', 'Ipoh, Perak', 30.00, '08:00 - 21:00', 'Parking, Drinking Water, Rest Area'),
('Heritage Hall', 'Table Tennis', 'Classic hall with 10 professional table tennis tables.', 'George Town, Penang', 12.00, '09:00 - 20:00', 'Locker Rooms, Parking, Coaching'),
('Victory Arena', 'Football', 'Mini football stadium with turf and 7-a-side pitch.', 'Kota Kinabalu, Sabah', 30.00, '08:00 - 22:00', 'Parking, Locker Rooms, Snack Bar'),
('Elite Tennis Center', 'Tennis', 'Premium tennis venue with clay courts and spectator seating.', 'Petaling Jaya, Selangor', 14.00, '07:00 - 21:00', 'Parking, Shower, Coaching Services'),
('Harborfront Court', 'Basketball', 'Indoor basketball court near the harbor with sea breeze cooling.', 'Kuching, Sarawak', 20.00, '08:00 - 21:00', 'Parking, Locker Rooms, Drinking Water'),
('Sunrise Badminton Club', 'Badminton', '24-hour badminton club with air-conditioned courts.', 'Seremban, Negeri Sembilan', 25.00, '06:00 - 00:00', 'Parking, WiFi, Refreshments'),
('Galaxy Sports Hall', 'Volleyball', 'Modern volleyball hall with 6 indoor courts.', 'Melaka City, Melaka', 35.00, '09:00 - 22:00', 'Parking, Locker Rooms, Refreshments'),
('Champion Arena', 'Table Tennis', 'Tournament-standard table tennis center with seating for spectators.', 'Johor Bahru, Johor', 12.00, '08:00 - 21:00', 'Parking, Locker Rooms, Coaching'),
('City Dome', 'Pickleball', 'Indoor dome with 8 pickleball courts and climate control.', 'Shah Alam, Selangor', 35.00, '07:00 - 23:00', 'WiFi, Parking, Lounge Area'),
('Lakeside Volleyball Court', 'Volleyball', 'Outdoor lakeside volleyball courts with great views.', 'Putrajaya Lake, Putrajaya', 20.00, '08:00 - 20:00', 'Parking, Rest Area, Refreshments');


-- venue_images data
-- Venue 1: Sunshine Court (Badminton)
INSERT INTO venue_images (venue_id, image_path) VALUES
(1, './Assets/venues/1_1.jpg'),
(1, './Assets/venues/1_2.jpg');

-- Venue 2: Sky Arena (Pickleball)
INSERT INTO venue_images (venue_id, image_path) VALUES
(2, './Assets/venues/2_1.jpg'),
(2, './Assets/venues/2_2.jpg'),
(2, './Assets/venues/2_3.jpg');

-- Venue 3: Ocean View Tennis Club (Tennis)
INSERT INTO venue_images (venue_id, image_path) VALUES
(3, './Assets/venues/3_1.jpg'),
(3, './Assets/venues/3_2.jpg');

-- Venue 4: Greenfield Stadium (Football)
INSERT INTO venue_images (venue_id, image_path) VALUES
(4, './Assets/venues/4_1.jpg'),
(4, './Assets/venues/4_2.jpg');

-- Venue 5: Summit Arena (Basketball)
INSERT INTO venue_images (venue_id, image_path) VALUES
(5, './Assets/venues/5_1.jpg'),
(5, './Assets/venues/5_2.jpg'),
(5, './Assets/venues/5_3.jpg');

-- Venue 6: Maple Sports Center (Volleyball)
INSERT INTO venue_images (venue_id, image_path) VALUES
(6, './Assets/venues/6_1.jpg'),
(6, './Assets/venues/6_2.jpg');

-- Venue 7: Heritage Hall (Table Tennis)
INSERT INTO venue_images (venue_id, image_path) VALUES
(7, './Assets/venues/7_1.jpg');

-- Venue 8: Victory Arena (Football)
INSERT INTO venue_images (venue_id, image_path) VALUES
(8, './Assets/venues/8_1.jpg');

-- Venue 9: Elite Tennis Center (Tennis)
INSERT INTO venue_images (venue_id, image_path) VALUES
(9, './Assets/venues/9_1.jpg');

-- Venue 10: Harborfront Court (Basketball)
INSERT INTO venue_images (venue_id, image_path) VALUES
(10, './Assets/venues/10_1.jpg'),
(10, './Assets/venues/10_2.jpg');

-- Venue 11: Sunrise Badminton Club (Badminton)
INSERT INTO venue_images (venue_id, image_path) VALUES
(11, './Assets/venues/11_1.jpg'),
(11, './Assets/venues/11_2.jpg'),
(11, './Assets/venues/11_3.jpg');

-- Venue 12: Galaxy Sports Hall (Volleyball)
INSERT INTO venue_images (venue_id, image_path) VALUES
(12, './Assets/venues/12_1.jpg'),
(12, './Assets/venues/12_2.jpg');

-- Venue 13: Champion Arena (Table Tennis)
INSERT INTO venue_images (venue_id, image_path) VALUES
(13, './Assets/venues/13_1.jpg'),
(13, './Assets/venues/13_2.jpg');

-- Venue 14: City Dome (Pickleball)
INSERT INTO venue_images (venue_id, image_path) VALUES
(14, './Assets/venues/14_1.jpg');

-- Venue 15: Lakeside Volleyball Court (Volleyball)
INSERT INTO venue_images (venue_id, image_path) VALUES
(15, './Assets/venues/15_1.jpg'),
(15, './Assets/venues/15_2.jpg');

-- promotions data
INSERT INTO promotions (title, description, image, expiry, discount_type, discount_value, venue_id, sport_category) VALUES
-- Venue-specific discounts
('Sunshine Court Deal', 'Book Sunshine Court and save RM10 on each session!', './Assets/promotions/sunshine.png', '2025-10-31', 'fixed', 10.00, 1, NULL),
('Victory Arena Special', '20% off all bookings at Victory Arena this season.', './Assets/promotions/victory.png', '2025-11-30', 'percent', 20.00, 8, NULL),

-- Sport-wide discounts
('Basketball Boost', 'Get RM15 off all basketball venues across Malaysia.', './Assets/promotions/basket.png', '2025-12-15', 'fixed', 15.00, NULL, 'Basketball'),
('Volleyball Fever', 'Enjoy 10% discount on all volleyball courts!', './Assets/promotions/volley.png', '2025-09-30', 'percent', 10.00, NULL, 'Volleyball'),
('Tennis Excellence', 'Flat RM20 off for every tennis venue booking.', './Assets/promotions/tennis.png', '2025-12-31', 'fixed', 20.00, NULL, 'Tennis'),
('Badminton Blast', '15% discount on all badminton clubs nationwide.', './Assets/promotions/bad.png', '2025-11-15', 'percent', 15.00, NULL, 'Badminton');



