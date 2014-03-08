DROP SCHEMA IF EXISTS lab3 CASCADE;
CREATE SCHEMA lab3;

CREATE TABLE lab3.customer ( cust_id serial PRIMARY KEY, 
							 poc_name varchar(50) NOT NULL );

CREATE TABLE lab3.invoice ( customer integer REFERENCES customer(cust_id), inv_no serial PRIMARY KEY, 
							invoice_date date NOT NULL, 
							street varchar(50) NOT NULL, 
							city varchar(50) NOT NULL, 
							state varchar(2) NOT NULL, 
							zipcode integer NOT NULL);

CREATE TABLE lab3.product ( name varchar(50) NOT NULL, 
							description varchar(100), 
							product_id serial PRIMARY KEY);

CREATE TABLE lab3.invoiceLine ( line_number serial PRIMARY KEY, 
								unit_price integer NOT NULL,
								quantity integer NOT NULL, 
								product_id integer REFERENCES product(product_id));

CREATE TABLE lab3.factory ( factory_id serial PRIMARY KEY, 
							phone_number varchar(25) );

CREATE TABLE lab3.factory_makes_products( factory_id integer REFERENCES factory,
										  product_id integer REFERENCES product,
										  PRIMARY KEY( factory_id, product_id));

CREATE TABLE lab3.employee ( employee_id serial PRIMARY KEY, 
							 first_name varchar(50) NOT NULL, 
							 last_name varchar(50) NOT NULL,
							 factory integer REFERENCES factory(factory_id));