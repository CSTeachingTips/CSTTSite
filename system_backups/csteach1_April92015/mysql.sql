-- cPanel mysql backup
GRANT USAGE ON *.* TO 'csteach1'@'localhost' IDENTIFIED BY PASSWORD '*0879DCD133DD31B3B0B4B9E151A468684C786C48';
GRANT ALL PRIVILEGES ON `csteach1\_test`.* TO 'csteach1'@'localhost';
GRANT ALL PRIVILEGES ON `csteach1\_wordpress`.* TO 'csteach1'@'localhost';
GRANT ALL PRIVILEGES ON `csteach1\_testing`.* TO 'csteach1'@'localhost';
GRANT ALL PRIVILEGES ON `csteach1\_mysql`.* TO 'csteach1'@'localhost';
GRANT ALL PRIVILEGES ON `csteach1\_%`.* TO 'csteach1'@'localhost';
GRANT USAGE ON *.* TO 'csteach1_admin'@'localhost' IDENTIFIED BY PASSWORD '*D549287A84092FC8C57CBCADAF75C84167B67707';
GRANT ALL PRIVILEGES ON `csteach1\_mysql`.* TO 'csteach1_admin'@'localhost';
GRANT USAGE ON *.* TO 'csteach1_cstt'@'localhost' IDENTIFIED BY PASSWORD '*C425DA177ED577002F4310002153B5F5BD048F65';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, EXECUTE, EVENT ON `csteach1\_test`.* TO 'csteach1_cstt'@'localhost';
GRANT ALL PRIVILEGES ON `csteach1\_testing`.* TO 'csteach1_cstt'@'localhost';
GRANT ALL PRIVILEGES ON `csteach1\_wordpress`.* TO 'csteach1_cstt'@'localhost';
GRANT ALL PRIVILEGES ON `csteach1\_mysql`.* TO 'csteach1_cstt'@'localhost';
