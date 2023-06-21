/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 8.0.23 : Database - development
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `pacientes` */

DROP TABLE IF EXISTS `pacientes`;

CREATE TABLE `pacientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `cedula` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `sexo` char(1) CHARACTER SET utf8 DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `correo` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `estado` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `pacientes` */

insert  into `pacientes`(`id`,`nombre`,`cedula`,`sexo`,`fecha_nacimiento`,`telefono`,`correo`,`estado`) values 
(1,'Paciente 00001','00001','M','1980-01-01','123456789','00001@correo.com',1),
(2,'Paciente 00002','00002','M','1990-01-01','123456789','00002@correo.com',1),
(3,'Paciente 00003','00003','F','2000-01-01','123456789','00003@correo.com',1),
(4,'Paciente 00004','00004','M','1970-01-01','123456789','00004@correo.com',1),
(5,'Paciente 00005','00005','F','1980-10-01','123456789','00005@correo.com',1),
(6,'Paciente 00006','00006','F','1940-01-01','123456789','00006@correo.com',1),
(7,'Paciente 00007','00007','M','2010-01-01','123456789','00007@correo.com',1),
(8,'Paciente 00008','00008','F','1999-01-01',NULL,'00008@correo.com',1),
(9,'Paciente ','00009','M','2004-06-05','2504652','paciente@correo.com',1);

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int NOT NULL DEFAULT '0',
  `usuario` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `nombre` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `clave` varchar(15) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`usuario`,`nombre`,`clave`) values 
(1,'administrador','Administrador','administrador12'),
(2,'analista','Analista','analista123'),
(3,'supervisor','Supervisor','supervisor123');

/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 8.0.23 : Database - development
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `pacientes` */

DROP TABLE IF EXISTS `pacientes`;

CREATE TABLE `pacientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `cedula` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `sexo` char(1) CHARACTER SET utf8 DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `correo` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `estado` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `pacientes` */

insert  into `pacientes`(`id`,`nombre`,`cedula`,`sexo`,`fecha_nacimiento`,`telefono`,`correo`,`estado`) values 
(1,'Paciente 00001','00001','M','1980-01-01','123456789','00001@correo.com',1),
(2,'Paciente 00002','00002','M','1990-01-01','123456789','00002@correo.com',1),
(3,'Paciente 00003','00003','F','2000-01-01','123456789','00003@correo.com',1),
(4,'Paciente 00004','00004','M','1970-01-01','123456789','00004@correo.com',1),
(5,'Paciente 00005','00005','F','1980-10-01','123456789','00005@correo.com',1),
(6,'Paciente 00006','00006','F','1940-01-01','123456789','00006@correo.com',1),
(7,'Paciente 00007','00007','M','2010-01-01','123456789','00007@correo.com',1),
(8,'Paciente 00008','00008','F','1999-01-01',NULL,'00008@correo.com',1),
(9,'Paciente ','00009','M','2004-06-05','2504652','paciente@correo.com',1);

/*Table structure for table `usuarios` */


DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int NOT NULL DEFAULT '0',
  `usuario` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `nombre` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `clave` varchar(15) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`usuario`,`nombre`,`clave`) values 
(1,'administrador','Administrador','administrador12'),
(2,'usuario','Usuario','usuario123'),
(3,'supervisor','Supervisor','supervisor123');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


/* MIS MODIFICACIONES */
ALTER TABLE pacientes
ADD tipo_sanguineo VARCHAR(10);

/* ADD COLUMN */
ALTER TABLE pacientes ADD COLUMN editando BOOL DEFAULT FALSE;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
