CREATE TABLE stores (
    id SMALLINT,
    name MEDIUMTEXT,
    jp MEDIUMTEXT
);

CREATE TABLE categories (
    id INTEGER,
    name MEDIUMTEXT
);

INSERT INTO stores VALUES
(1, 'ebay', 'ebay'),
(2, 'buyma', 'BUYMA'),
(3, 'etoren', 'ETOREN')
;

CREATE TABLE product_cates (
    id INTEGER,
    name MEDIUMTEXT,
    path_name MEDIUMTEXT,
    relation MEDIUMTEXT,
    updated_at MEDIUMTEXT
);

CREATE TABLE brand_codes (
    id INTEGER,
    name MEDIUMTEXT,
    jtitle MEDIUMTEXT,
    path_name MEDIUMTEXT,
    relation MEDIUMTEXT,
    updated_at MEDIUMTEXT
);

CREATE TABLE all_categories (
    name MEDIUMTEXT
);

CREATE TABLE last_status (
    store_id SMALLINT,
    product_cate_id INTEGER,
    brand_code_id INTEGER,
    all_categorie_id INTEGER
);

CREATE TABLE templates (
    name TEXT,
    value TEXT
);

CREATE TABLE histories (
    name TEXT
);

CREATE TABLE users (
    name TEXT,
    password TEXT,
    client_id TEXT,
    secret TEXT,
    access_token TEXT,
    refresh_token TEXT,
    refresh_time TEXT
);

CREATE TABLE calc_rules (
    from_price INT,
    to_price INT,
    bai FLOAT,
    tasu INT
);

INSERT INTO calc_rules VALUES (0, 50000, 2.0, 600), (50000, 100000, 1.7, 1000), (100000, 10000000, 1.5, 300);

load data local 
    infile 'C:\\Users\\s.iijima\\Desktop\\YUploader関連\\YUploader\\product_cates.csv'
into table
    product_cates
fields
    terminated by ','
;