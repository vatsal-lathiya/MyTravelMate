-- Database : MTM

-- Allow to Execute Query In By Given Database Name
USE MTM;

CREATE TABLE IF NOT EXISTS tbl_admauth(
    adm_id INT AUTO_INCREMENT PRIMARY KEY,
    adm_email VARCHAR(50) NOT NULL UNIQUE,
    adm_psw VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tbl_uauth(
    u_id INT AUTO_INCREMENT PRIMARY KEY,
    u_name VARCHAR(50) NOT NULL,
    u_email VARCHAR(100) NOT NULL UNIQUE,
    u_psw VARCHAR(255) NOT NULL,
    u_phone VARCHAR(15),
    u_logo VARCHAR(255),
    u_status ENUM('Active','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tbl_states(
    state_id INT AUTO_INCREMENT PRIMARY KEY,
    state_name VARCHAR(100) NOT NULL,
    state_code int,
    state_status ENUM('Available','Unavailable') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tbl_destination(
    dest_id INT AUTO_INCREMENT PRIMARY KEY,
    state_id INT NOT NULL,
    dest_name VARCHAR(100) NOT NULL,
    state_name VARCHAR(100),
    dest_desc TEXT,
    dest_besttime VARCHAR(50),
    dest_img VARCHAR(255),
    dest_status ENUM('Open','Close') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (state_id) REFERENCES tbl_states(state_id)
);

CREATE TABLE IF NOT EXISTS tbl_vehicletype(
    vehicletype_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_type ENUM('Car','Bus','Train','Airplane') NOT NULL
);

CREATE TABLE IF NOT EXISTS tbl_vehicle(
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicletype_id INT NOT NULL,
    vehicle_name VARCHAR(100) NOT NULL,
    capacity INT,
    vehicle_status ENUM('Available','Unavailable') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicletype_id) REFERENCES tbl_vehicletype(vehicletype_id)
);

CREATE TABLE IF NOT EXISTS tbl_package(
    pkg_id INT AUTO_INCREMENT PRIMARY KEY,
    pkg_name VARCHAR(100) NOT NULL,
    from_dest_id INT NOT NULL,
    to_dest_id INT NOT NULL,
    pkg_duration VARCHAR(50),
    pkg_price DECIMAL(10,2),
    pkg_desc TEXT,
    pkg_img VARCHAR(255),
    pkg_status ENUM('Active','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_dest_id) REFERENCES tbl_destination(dest_id),
    FOREIGN KEY (to_dest_id) REFERENCES tbl_destination(dest_id)
);

CREATE TABLE IF NOT EXISTS tbl_blogs(
    blog_id INT AUTO_INCREMENT PRIMARY KEY,
    blog_title VARCHAR(255),
    blog_desc TEXT,
    pkg_id INT,
    dest_id INT,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pkg_id) REFERENCES tbl_package(pkg_id),
    FOREIGN KEY (dest_id) REFERENCES tbl_destination(dest_id)
);

CREATE TABLE IF NOT EXISTS tbl_gallery(
    g_id INT AUTO_INCREMENT PRIMARY KEY,
    g_img VARCHAR(255),
    dest_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dest_id) REFERENCES tbl_destination(dest_id)
);

CREATE TABLE IF NOT EXISTS tbl_contact(
    c_id INT AUTO_INCREMENT PRIMARY KEY,
    c_email VARCHAR(100),
    c_subject VARCHAR(150),
    c_message TEXT,
    u_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (u_id) REFERENCES tbl_uauth(u_id)
);

CREATE TABLE IF NOT EXISTS tbl_booking(
    b_id INT AUTO_INCREMENT PRIMARY KEY,
    pkg_id INT NOT NULL,
    u_id INT NOT NULL,
    vehicletype_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    b_travel_date DATE,
    b_persons INT,
    b_pkg_price DECIMAL(10,2),
    b_dis_price DECIMAL(10,2),
    b_total_price DECIMAL(10,2),
    b_status ENUM('Confirm','Pending') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pkg_id) REFERENCES tbl_package(pkg_id),
    FOREIGN KEY (u_id) REFERENCES tbl_uauth(u_id),
    FOREIGN KEY (vehicletype_id) REFERENCES tbl_vehicletype(vehicletype_id),
    FOREIGN KEY (vehicle_id) REFERENCES tbl_vehicle(vehicle_id)
);

CREATE TABLE IF NOT EXISTS tbl_setting(
    s_id INT AUTO_INCREMENT PRIMARY KEY,
    web_name VARCHAR(100),
    adm_logo VARCHAR(255),
    adm_footer TEXT,
    web_link TEXT
);







