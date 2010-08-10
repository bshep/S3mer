-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: s3mer
-- ------------------------------------------------------
-- Server version	5.1.41-3ubuntu12.6

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text,
  `begin` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
INSERT INTO `ads` VALUES (2,'http://media1.s3mer.com.s3.amazonaws.com/ads/s3merPromoGreen.m4v',NULL,NULL),(3,'http://media1.s3mer.com.s3.amazonaws.com/ads/s3merPromoMagenta.m4v',NULL,NULL),(4,'http://media1.s3mer.com.s3.amazonaws.com/ads/s3merPromoBlue.m4v',NULL,NULL);
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asrunlog`
--

DROP TABLE IF EXISTS `asrunlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asrunlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `file_type` tinytext,
  `time_end` bigint(21) DEFAULT NULL,
  `time_start` bigint(21) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `show_id` int(11) DEFAULT NULL,
  `file` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asrunlog`
--

LOCK TABLES `asrunlog` WRITE;
/*!40000 ALTER TABLE `asrunlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `asrunlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batchcontrol`
--

DROP TABLE IF EXISTS `batchcontrol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batchcontrol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batchrunning` int(1) DEFAULT '0',
  `description` varchar(20) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batchcontrol`
--

LOCK TABLES `batchcontrol` WRITE;
/*!40000 ALTER TABLE `batchcontrol` DISABLE KEYS */;
INSERT INTO `batchcontrol` VALUES (1,0,'ipn process','2010-08-10 21:53:01'),(2,1,'thumbnails','2009-10-25 05:22:01'),(3,1,'purge deleted','2010-01-15 05:34:56'),(4,0,'db backup','2010-03-28 23:31:45');
/*!40000 ALTER TABLE `batchcontrol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `channel`
--

DROP TABLE IF EXISTS `channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channelname` varchar(45) NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  `createdon` datetime NOT NULL,
  `shared` int(1) unsigned NOT NULL DEFAULT '0',
  `defaultshow` int(10) unsigned NOT NULL,
  `mediaurl` varchar(200) NOT NULL,
  `configurl` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `channel`
--

LOCK TABLES `channel` WRITE;
/*!40000 ALTER TABLE `channel` DISABLE KEYS */;
/*!40000 ALTER TABLE `channel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `channelschedule`
--

DROP TABLE IF EXISTS `channelschedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channelschedule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `showid` int(10) unsigned NOT NULL,
  `Mon` int(1) unsigned NOT NULL DEFAULT '1',
  `Tue` int(1) unsigned NOT NULL DEFAULT '1',
  `Wed` int(1) unsigned NOT NULL DEFAULT '1',
  `Thu` int(1) unsigned NOT NULL DEFAULT '1',
  `Fri` int(1) unsigned NOT NULL DEFAULT '1',
  `Sat` int(1) unsigned NOT NULL DEFAULT '1',
  `Sun` int(1) unsigned NOT NULL DEFAULT '1',
  `startdate` date NOT NULL DEFAULT '2000-01-01',
  `enddate` date NOT NULL DEFAULT '3000-01-01',
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `channel` int(10) unsigned NOT NULL,
  `effect` int(11) NOT NULL DEFAULT '1',
  `order` int(11) NOT NULL,
  `AM` int(1) NOT NULL DEFAULT '0',
  `PM` int(1) NOT NULL DEFAULT '0',
  `ns` int(1) NOT NULL DEFAULT '1',
  `temporder` int(11) NOT NULL DEFAULT '0',
  `deleteflag` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `channelschedule`
--

LOCK TABLES `channelschedule` WRITE;
/*!40000 ALTER TABLE `channelschedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `channelschedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contents`
--

DROP TABLE IF EXISTS `contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(10) DEFAULT NULL,
  `contentname` varchar(200) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=382 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contents`
--

LOCK TABLES `contents` WRITE;
/*!40000 ALTER TABLE `contents` DISABLE KEYS */;
INSERT INTO `contents` VALUES (1,'es','Dynamic Digital Signage For Humans','Publicidad en la era digital para humanos'),(2,'pt','Dynamic Digital Signage For Humans','Publicidade na era digital dispon&iacute;vel para humanos'),(3,'en','WelcomeMessage','Welcome to <strong>s3mer</strong> the best and easiest way to take advantage of <span class=\"hilite\">dynamic digital signage</span> technology for your business.'),(4,'es','WelcomeMessage','Bienvenido a <strong>s3mer</strong>, la forma m&aacute;s f&aacute;cil de tomar ventaja de la <span class=\"hilite\">era digital</span> para la publicidad de su negocio.'),(5,'pt','WelcomeMessage','Bem-vindo ao <strong>s3mer</strong>, a maneira mais f&aacute;cil de aproveitar a tecnologia da <span class=\"hilite\">era digital</span> na publicidade do seu negocio.'),(6,'es','Login','Entra'),(7,'pt','Login','Entra'),(12,'en','RegisterMessage','Register for our <span class=\"hilite\">free beta</span>, to be one of the first to try the simplest way of taking control of your displays.'),(8,'es','Register Free','Crea una cuenta'),(9,'pt','Register Free','Cria uma conta'),(10,'es','or','o'),(11,'pt','or','ou'),(13,'es','RegisterMessage','Reg&iacute;strese para nuestro software <span class=\"hilite\">beta gratis</span> y sea uno de los primeros en probar la manera m&aacute;s simple de tomar control de sus pantallas.'),(14,'pt','RegisterMessage','Cadastre-se para tentar nosso software <span class=\"hilite\">beta gratuitamente</span>, e seja um dos primeiros em ver a maneira mais f&aacute;cil e simple de  controlar as suas telas.'),(15,'en','UnlikeTraditionalMessage','s3mer provides a cross-platform solution developed to work great both on <span class=\"hilite\">Windows</span> and <span class=\"hilite\">Mac OS X</span>.'),(16,'es','UnlikeTraditionalMessage','A diferencia de los paquetes tradicionales de software, s3mer provee una soluci&oacute;n interplataforma desarrollada para trabajar en <span class=\"hilite\">Windows</span> y <span class=\"hilite\">Mac OS X</span>'),(17,'pt','UnlikeTraditionalMessage','Ao contr&aacute;rio dos paquetes tradicionais de software, s3mer d&aacute; uma solu&ccedil;&atilde;o multiplataforma que funciona no <span class=\"hilite\">Windows</span> e <span class=\"hilite\">Mac OS X</span>'),(18,'pt','Bring your old computer','Traga seu computador velho'),(19,'es','Bring your old computer','Traiga su computadora vieja'),(20,'pt','WhereRunMessage','Voc&ecirc; pode rodar o s3mer em <span class=\"hilite\">quase qualquer computador fabricado nos pasados 5 anos</span>. Tudo o  <span class=\"hilite\">gerenciamento de contenido &eacute; feito a trav&eacute;s do internet</span> desde qualquer parte do planeta.'),(21,'en','WhereRunMessage','You can run s3mer in <span class=\"hilite\">almost any computer made in the last 5 years</span>. All of the <span class=\"hilite\">management is done via web browser</span> from anywhere in the planet.'),(22,'es','WhereRunMessage','Usted puede correr s3mer en <span class=\"hilite\">casi cualquier computadora fabricada en los pasados 5 a&ntilde;os</span>. Todo el <span class=\"hilite\">manejo de contenido se realiza a trav&eacute;s del internet</span> desde cualquier parte del planeta.'),(23,'en','Registeryourself','<span class=\"hilite\">Register</span> fill out some info about you'),(24,'pt','Registeryourself','<span class=\"hilite\">Cadastre-se</span>, d&ecirc;-nos informa&ccedil;&atilde;o sobre voc&ecirc;.'),(25,'es','Registeryourself','<span class=\"hilite\">Reg&iacute;strese</span>, denos informaci&oacute;n acerca de usted y su negocio.'),(26,'en','SelectTemplate','<span class=\"hilite\">Select a template</span> we have lots of profesinally designed templates for you to choose.'),(27,'pt','SelectTemplate','<span class=\"hilite\">Selecione um molde</span>, temos muitos moldes feitos previamente para voc&ecirc; escolher.'),(28,'es','SelectTemplate','<span class=\"hilite\">Seleccione una plantilla</span>, tenemos plantillas predise&ntilde;adas profesionalmente para que usted escoja.'),(29,'en','UploadMedia','<span class=\"hilite\">Upload Media</span> upload photos and videos'),(30,'es','UploadMedia','<span class=\"hilite\">Haga upload</span> de fotos y videos'),(31,'pt','UploadMedia','<span class=\"hilite\">Fa&ccedil;a upload</span> de fotos e v&iacute;deos'),(32,'en','InstallPlayer','<span class=\"hilite\">Install player application</span> with one click'),(33,'es','InstallPlayer','<span class=\"hilite\">Instale la aplicaci&oacute;n</span> con un solo click.'),(34,'pt','InstallPlayer','<span class=\"hilite\">Instale a aplica&ccedil;&atilde;o</span> com um s&oacute; clique.'),(35,'es','Get up and runing in 4 easy steps','Siga solo 4 f&aacute;ciles pasos y listo'),(36,'pt','Get up and runing in 4 easy steps','S&oacute; 4 f&aacute;ceis passos e fique pronto'),(37,'es','Dozens of simple to use and powerfull features that will make your screens look awsome','M&uacute;ltiples efectos. Simples, pero poderosos que har&aacute;n la diferencia en sus presentaciones'),(38,'pt','Dozens of simple to use and powerfull features that will make your screens look awsome','M&uacute;ltiples fun&ccedil;&otilde;es. Simples, mas poderosas. Suas apresenta&ccedil;&otilde;es ficaram excelentes'),(39,'es','LiveVideo','Entrada para <span class=\"hilite\">video en vivo</span>'),(40,'en','LiveVideo','<span class=\"hilite\">Live video</span> input'),(41,'pt','LiveVideo','Entrada para <span class=\"hilite\">v&iacute;deo ao vivo</span>'),(42,'en','SubscribePodcasts','Subscribe to any of the hundreds of video <span class=\"hilite\">Podcasts</span> available online'),(43,'es','SubscribePodcasts','Subscribase a cualquiera de los cientos de <span class=\"hilite\">Podcasts</span> disponibles en l&iacute;nea.'),(44,'pt','SubscribePodcasts','Subscreva-se a qualquer dos <span class=\"hilite\">Podcasts</span> dispon&iacute;veis em linha'),(45,'es','ShowScheduling','Programaci&oacute;n de media'),(46,'pt','ShowScheduling','Programa&ccedil;&aacute;o de media'),(47,'es','Image Files','Archivos de im&aacute;genes'),(48,'pt','Image Files','Arquivos de imagens'),(49,'es','Multiple Shows with one player','M&uacute;ltiples shows con un solo equipo'),(50,'pt','Multiple Shows with one player','M&uacute;ltiples espet&aacute;culos com um s&oacute; equipamento'),(56,'pt','Show scheduling','Programa&ccedil;&atilde;o de Espet&aacute;culos'),(54,'pt','Workstation mode','Modo Workstation'),(55,'es','Show scheduling','Planificaci&oacute;n de Shows'),(57,'es','Multiple Shows with one player','M&uacute;ltiples pantallas en una sola computadora'),(58,'pt','Multiple Shows with one player','M&uacute;ltiples telas num s&oacute; computador'),(59,'es','Video formats','Formatos de Video'),(60,'pt','Video formats','Formatos de V&iacute;deos'),(61,'es','Image Files','Archivos de Im&aacute;genes'),(62,'pt','Image Files','Arquivos de Imagens'),(63,'es','Flash Movies','Pel&iacute;culas Flash'),(64,'pt','Flash Movies','Filmes Flash'),(65,'en','SubscribeRSS','Subscribe to <span class=\"hilite\">RSS</span> Feeds to recieve the latest news'),(66,'es','SubscribeRSS','Subscribase a sus <span class=\"hilite\">RSS</span> Feeds favoritos'),(67,'pt','SubscribeRSS','Subscreva-se a seus <span class=\"hilite\">RSS</span> Feeds favoritos'),(68,'en','OurMission','<span class=\"hilite\">Our mission</span> is to bring the every feature of &quot;high end&quot; digital signage system to the masses.'),(69,'es','OurMission','<span class=\"hilite\">Nuestra misi&oacute;n</span> es llevar cada caracter&iacute;stica que utilizan los profesionales a las masas.'),(70,'pt','OurMission','<span class=\"hilite\">Nossa miss&atilde;o</span> &eacute; levar cada caracter&iacute;stica que os professionais utilizam a todos.'),(71,'en','EnterAdmin','To enter the <span class=\"hilite\">administrative area</span> you must login now.'),(72,'es','EnterAdmin','Para entrar al <span class=\"hilite\">area administrativa</span>, tiene que entrar sus credenciales ahora.'),(73,'pt','EnterAdmin','Para entrar na area administrativa, deve entrar com seu nome de usu&aacute;rio e senha agora.'),(85,'es','Address','Direcci&oacute;n'),(74,'es','Remember Me','Recu&eacute;rdame'),(75,'pt','Remember Me','Recorde-me'),(76,'es','Lost Password?','&#191;Olvid&oacute; su contrase&ntilde;a?'),(77,'pt','Lost Password?','Esqueceu seu senha?'),(78,'es','Login','Entrar'),(79,'pt','Login','Entrar'),(82,'en','FillInformation','Please fill <span class=\"hilite\">all</span> the information required below to create new account. You must <span class=\"hilite\">agree to the terms and conditions</span> before submiting.'),(83,'es','FillInformation','Por favor, llene <span class=\"hilite\">toda</span> la informaci&oacute;n requerida para crear una nueva cuenta. Usted <span class=\"hilite\">debe estar de acuerdo con los t&eacute;rminos y condiciones</span> antes de someter su informaci&oacute;n'),(84,'pt','FillInformation','Por favor, preencha <span class=\"hilite\">tuda</span> a informa&ccedil;&atilde;o requerida para criar uma nova conta. Voc&ecirc; deve concordar com os termos e condi&ccedil;&otilde;es do servi&ccedil;o.'),(95,'pt','Re-type','Confirme'),(86,'pt','Address','Endere&ccedil;o'),(87,'es','Phones','Tel&eacute;fonos'),(88,'pt','Phones','Fones'),(89,'en','WeWantHearYou','<span class=\"hilite\">We want to hear from</span> you and your experience using s3mer.'),(90,'es','WeWantHearYou','<span class=\"hilite\">Queremos escucarte</span> y saber tus experiencias con s3mer.'),(91,'pt','WeWantHearYou','<span class=\"hilite\">Queremos ouvir voc&ecirc;</span> e conocer seus experi&ecirc;ncias s3mer.'),(92,'es','Social Nets','Redes Sociales'),(93,'pt','Social Nets','Redes Sociais'),(94,'es','Re-type','Confirme'),(96,'es','Password','Contrase&ntilde;a'),(97,'pt','Password','Senha'),(98,'es','First Name','Nombre'),(99,'pt','First Name','Nome'),(100,'pt','Last Name','Sobrenome'),(101,'es','Last Name','Apellido'),(102,'es','Address','Direcci&oacute;n'),(103,'pt','Address','Endere&ccedil;o'),(104,'es','State','Estado'),(105,'pt','State','Estado'),(106,'es','Province','Provincia'),(107,'pt','Province','Prov&iacute;ncia'),(108,'es','Country','Pa&iacute;s'),(109,'pt','Country','Pa&iacute;s'),(110,'pt','Zip','CEP'),(111,'es','Postal Code','C&oacute;digo Postal'),(112,'pt','Postal Code','C&oacute;digo Postal'),(113,'es','Industry','Industria'),(114,'pt','Industry','Ind&uacute;stria'),(115,'es','I want to recieve a periodical newsletter','Quiero recibir las publicaciones de s3mer'),(116,'pt','I want to recieve a periodical newsletter','Quero receber not&iacute;cias de s3mer'),(117,'es','I have read and agree to the','He leido y aceptado los'),(118,'pt','I have read and agree to the','Hei lido y aceitado os'),(119,'es','terms and conditions','t&eacute;rminos y condiciones'),(120,'pt','terms and conditions','termos e condi&ccedil;&otilde;es'),(121,'es','Show Map','Mostrar Mapa'),(122,'pt','Show Map','Mostrar Mapa'),(123,'en','RegSuccess','Your registration was <span class=\"hilite\">successful</span>. We have sent you a copy of your information to:'),(124,'es','RegSuccess','Su registraci&oacute;n fue exitosa. Hemos enviado una copia de la informaci&oacute;n de su cuenta a:  '),(125,'pt','RegSuccess','Seu cadastro foi feito com sucesso.  A informação de seu conta foi enviada através do email: '),(126,'es','Click Here to login and start','Haga click aqu&iacute; para comenzar'),(127,'pt','Click Here to login and start','Clique aqui para entrar e iniciar a sess&atilde;o'),(128,'es','Company Information','Informaci&oacute;n de la Compa&ntilde;&iacute;a'),(129,'pt','Company Information','Informa&ccedil;&atilde;o da Empresa'),(130,'en','CompanyInfo','s3mer inc. was founded in January of 2008 in San Juan, Puerto Rico by Giovanni Collazo, Arnaldo Rivera, Bruce Sheplan, Heriberto Roque and Ivette Ayala.'),(133,'es','Terms of Use','T&eacute;rminos y condiciones de uso'),(134,'pt','Terms of Use','Termos e condi&ccedil;&otilde;es de uso'),(138,'es','Copyright','Derechos'),(139,'pt','Copyright','Dereitos'),(143,'es','Register','Registrar'),(144,'pt','Register','Cadastrar'),(145,'en','ToDownloadInstall','<p>To download and install the player software <span class=\"hilite\">click on Install Now</span> below. Only press this button if you are on the computer that will be used as a player and connected to the monitors.</p>'),(146,'es','ToDownloadInstall','Para descargar e instalar el software reproductor <span class=\"hilite\">haga click en Instalar Ahora</span> abajo. Solo tiene que presionar el boton si est&aacute; en la computadora que ser&aacute; usado como reproductor y conectada a el o los monitores. '),(147,'pt','ToDownloadInstall','Para descargar e instalar el software reproductor <span class=\"hilite\">haga click en Instalar Ahora</span> abajo. Solo tiene que presionar el boton si est&aacute; en la computadora que ser&aacute; usado como reproductor y conectada a el o los monitores. '),(148,'es','System Requirements','Requisitos de Sistema'),(149,'pt','System Requirements','Requisitos do Sistema'),(150,'es','logout','salir'),(151,'pt','logout','sair'),(152,'es','Intel Pentium 2GHz or faster processor','Intel Pentium 2GHz o mejor'),(153,'pt','Intel Pentium 2GHz or faster processor','Intel Pentium 2GHz ou melhor'),(154,'es','Windows 2000 with Service Pack 4; Windows XP with Service Pack 2; or Windows Vista Home Premium, Business, Ultimate, or Enterprise','Windows 2000 con Service Pack 4; Windows XP con Service Pack 2; o Windows Vista Home Premium, Business, Ultimate, or Enterprise'),(155,'pt','Windows 2000 with Service Pack 4; Windows XP with Service Pack 2; or Windows Vista Home Premium, Business, Ultimate, or Enterprise','Windows 2000 com Service Pack 4; Windows XP com Service Pack 2; ou Windows Vista Home Premium, Business, Ultimate, or Enterprise'),(156,'es','DirectX 8.0 or higher','DirectX 8.0 o m&aacute;s reciente'),(157,'pt','DirectX 8.0 or higher','DirectX 8.0 ou mais novo'),(158,'es','512MB of RAM; 32MB of VRAM','512MB de RAM; 32MB de VRAM'),(159,'pt','512MB of RAM; 32MB of VRAM','512MB de RAM; 32MB de VRAM'),(160,'es','PowerPC G4 1.8GHz or faster processor or Intel Core Duo 1.33GHz or faster processor','Procesador PowerPC G4 1.8GHz, Intel Core Duo 1.33GHz o superior'),(161,'pt','PowerPC G4 1.8GHz or faster processor or Intel Core Duo 1.33GHz or faster processor','Processador PowerPC G4 1.8GHz, Intel Core Duo 1.33GHz ou melhor'),(162,'es','Mac OS X v.10.4.9 or later or 10.5.1 (Intel or PowerPC; Intel processor required for H.264 video)','Mac OS X v.10.4.9 o m&aacute;s reciente. Para video H.264 se requiere procesador Intel y Mac OS X 10.5.2'),(163,'pt','Mac OS X v.10.4.9 or later or 10.5.1 (Intel or PowerPC; Intel processor required for H.264 video)','Mac OS X v.10.4.9 ou mais novo. Para video H.264 &eacute; preciso procesador Intel e Mac OS X 10.5.2'),(164,'en','ForLiveVideo','For live video playback your system must be equipped with a <span class=\"hilite\">Firewire</span> connection and you must get the <span class=\"hilite\">Canopus ADVC55</span> for converting analog video.'),(165,'es','ForLiveVideo','Para video en vivo su sistema debe estar equipado con un puerto <span class=\"hilite\">Firewire</span>. Es necesario el convertidor de video an&aacute;logo-digital <span class=\"hilite\">Canopus ADVC55</span>'),(166,'pt','ForLiveVideo','Para video ao vivo su sistema deve ter um porto <span class=\"hilite\">Firewire</span>. Precisa o conversor de video an&aacute;logo-digital <span class=\"hilite\">Canopus ADVC55</span>'),(168,'es','Last Heartbeat','&Uacute;ltimo latido'),(169,'pt','Last Heartbeat','&Uacute;ltima vista'),(170,'es','Click here to create a new player','Haga click aqu&iacute; para crear un nuevo reproductor'),(171,'pt','Click here to create a new player','Clique aqui para criar um novo cliente'),(172,'es','New Player','Nuevo Reproductor'),(173,'pt','New Player','Novo cliente'),(174,'es','Press here to edit this player','Presione aqu&iacute; para editar este reproductor'),(175,'pt','Press here to edit this player','Clique aqui para alterar este cliente'),(176,'en','RecoverPassword','To <span class=\"hilite\">recover your password</span> enter the <span class=\"hilite\">email</span> address you used for <span class=\"hilite\">registration</span>. We will email you current password in a couple of seconds.'),(177,'es','RecoverPassword','Para <span class=\"hilite\">recuperar su contrase&ntilde;a</span> entre su direcci&oacute;n de <span class=\"hilite\">correo electr&oacute;nico</span> que utiliz&oacute; durante el proceso de <span class=\"hilite\">registraci&oacute;n</span>. Enviaremos la contrase&ntilde;a a su correo electr&oacute;nico en unos segundos.'),(178,'pt','RecoverPassword','Para <span class=\"hilite\">recuperar a sua senha</span>, entre com o endere&ccedil;o eletr&ocirc;nico que usou durante o processo do registo'),(179,'en','NoUserFound','Your username has not been found in our database'),(180,'es','NoUserFound','Su nombre de usuario no ha sido encontrado en nuestra base de datos'),(181,'pt','NoUserFound','Seu nome de usuario n&atilde;o foi encontrado no nossa base de dados'),(182,'en','SendPasswordSuccess','Your password has been successfully sent to: '),(183,'es','SendPasswordSuccess','Su contrase&ntilde;a ha sido enviada exitosamente a su correo: '),(184,'pt','SendPasswordSuccess','Sua senha foi enviada a seu correio eletr&oacute;nico : '),(185,'en','HereAreAnswers','Here are the <span class=\"hilite\">answers</span> to some of the <span class=\"hilite\">frequently asked questions</span>. If you don\'t find your answer please feel free to <span class=\"hilite\">submit it</span> using the form at the bottom of the page.'),(186,'es','HereAreAnswers','Aqu&iacute; est&aacute;n las <span class=\"hilite\">respuestas</span> a algunas de las <span class=\"hilite\">preguntas m&aacute;s frecuentes</span>. Si usted no encuentra la respuesta a su pregunta, som&eacute;tala usando la forma al final de esta p&aacute;gina.'),(187,'en','ForCompleteDocumentation','For <span class=\"hilite\">complete and up-to-date documentation</span> visit the <a href=\"wiki.php\">s3mer Documentation and Support Wiki</a>'),(188,'es','ForCompleteDocumentation','Para <span class=\"hilite\">informaci&oacute;n y documentaci&oacute;n completa y al d&iacute;a</span> visite el <a href=\"wiki.php\">Wiki de s3mer</a>'),(304,'en','NoteBeta','Note: We are currently BETA testing our service. The features available during the BETA will be the same as the ones available for our \'S3mer Free\' service. When we launch the \'S3mer Pro\' service there will be additional features available for a nominal fee.'),(191,'es','Index','&Iacute;ndice'),(192,'pt','Index','&Iacute;ndice'),(193,'es','Answers','Respuestas'),(194,'pt','Answers','Respostas'),(195,'es','Submit a question','Someter una pregunta'),(196,'pt','Submit a question','Fa&ccedil;a uma pergunta'),(197,'es','Submit','Someter'),(198,'pt','Submit','Submeta'),(199,'es','FAQ','Preguntas Frecuentes'),(200,'pt','FAQ','Preguntas Frecuentes'),(201,'pt','add.gif','adicionar.gif'),(202,'es','S','D'),(203,'pt','S','D'),(204,'es','M','L'),(205,'pt','M','2'),(206,'es','T','M'),(207,'pt','T','3'),(208,'es','W','Mi'),(209,'pt','W','4'),(210,'es','Th','J'),(211,'pt','Th','5'),(212,'es','F','V'),(213,'pt','F','6'),(214,'es','Sa','S'),(215,'pt','Sa','S'),(216,'es','Show Name','Nombre del Espect&aacute;culo'),(217,'es','Choose or change show template','Seleccione o cambie el dise&ntilde;o del espect&aacute;culo'),(218,'es','Click on a region to edit contents','Haga click en una regi&oacute;n para editar los contenidos'),(219,'es','RSS Region','Regi&oacute;n RSS'),(220,'pt','RSS Region','Regi&atilde;o RSS'),(221,'es','Main Media Region','Regi&oacute;n Principal'),(222,'pt','Main Media Region','Regi&atilde;o Principal'),(223,'es','cancel-up.gif','cancelar-up.gif'),(224,'pt','cancel-up.gif','cancelar-up.gif'),(225,'es','save.gif','guardar.gif'),(226,'pt','save.gif','salvar.gif'),(227,'es','save-down.gif','guardar-down.gif'),(228,'pt','save-down.gif','salvar-down.gif'),(229,'es','save-up.gif','guardar-up.gif'),(230,'pt','save-up.gif','salvar-up.gif'),(231,'es','cancel-down.gif','cancelar-down.gif'),(232,'pt','cancel-down.gif','cancelar-down.gif'),(233,'es','cancel.gif','cancelar.gif'),(234,'pt','cancel.gif','cancelar.gif'),(235,'es','Side Bar','Barra Lateral'),(236,'es','Save','Guardar'),(237,'pt','Save','Salvar'),(238,'es','Cancel','Cancelar'),(239,'pt','Cancel','Cancelar'),(243,'pt','Files','Arquivos'),(242,'pt','Live','Ao Vivo'),(244,'pt','seconds','segundos'),(245,'pt','Click on a region to edit contents','Selecione uma regi&atilde;o'),(246,'pt','Choose or change show template','Selecione um molde'),(247,'es','Live','En Vivo'),(248,'es','Files','Archivos'),(250,'es','Add Shows to Player Playlist','A&ntilde;adir Espect&aacute;culos'),(251,'pt','Add Shows to Player Playlist','Adicionar Espet&aacute;culos'),(252,'pt','Delete','Excluir'),(253,'es','Other','Otra'),(254,'pt','Other','Outra'),(255,'es','If other explain','Si escogi&oacute; otra por favor explique'),(256,'pt','If other explain','Explique por favor'),(257,'es','Request Password','Recuperar contrase&ntilde;a'),(258,'pt','Request Password','Recuperar senha'),(259,'pt','City','Cidade'),(260,'pt','Privacy Policy','Pol&iacute;tica de Privacidade'),(261,'pt','Select Player Type','Selecione o tipo de reproductor'),(262,'es','Type to search files','Entre el archivo que desea buscar'),(263,'pt','Type to search files','Entre com o nome do arquivo que deseja procurar'),(264,'pt','New Folder','Nova Pasta'),(265,'pt','Public','P&uacute;blico'),(266,'pt','All','Tudo'),(267,'pt','All Videos','Videos'),(268,'pt','All Images','Imagens'),(269,'es','Public','P&uacute;blico'),(270,'es','All','Todo'),(271,'es','All Videos','Videos'),(272,'es','All Images','Im&aacute;genes'),(273,'es','New Folder','Nueva Carpeta'),(274,'pt','Disk Space','Espa&ccedil;o no disco'),(275,'es','Disk Space','Espacio en disco'),(276,'es','Delete','Eliminar'),(277,'es','Next','Pr&oacute;ximo'),(278,'es','Previous','Anterior'),(279,'es','Delete Selected Files','Eliminar Archivos Seleccionados'),(280,'es','None','Ninguno'),(281,'es','New','A&ntilde;adir'),(282,'es','Add','A&ntilde;adir'),(283,'es','Select ONE or MULTIPLE Shows to add','Seleccione uno o m&aacute;s espect&aacute;culos para a&ntilde;adir'),(285,'es','Library','Libreria'),(286,'es','Main','Principal'),(287,'es','Main','Principal'),(288,'pt','Main','Principal'),(289,'es','Select','Seleccione'),(290,'pt','Select','Selecione'),(291,'pt','None','Nada'),(292,'pt','Delete Selected Files','Excluir Arquivos Selecionados'),(293,'pt','New','Adicionar'),(294,'pt','New Show','Adicionar Espet&aacute;culo'),(295,'pt','Click here to create a new show','Clique aqui para adicionar um espet&aacute;culo'),(296,'es','Click here to create a new show','Haga click aqu&iacute; para a&ntilde;adir un espect&aacute;culo nuevo'),(297,'es','New Show','A&ntilde;adir Espect&aacute;culo'),(298,'es','Edit','Editar'),(299,'pt','Edit','Alterar'),(300,'pt','Enable','Ligar'),(301,'pt','Disable','Desligar'),(302,'es','Enable','Activar'),(303,'es','Disable','Desactivar'),(305,'es','NoteBeta','Nota: Estamos en etapa de BETA p&uacute;blico. Las caracter&iacute;sticas disponibles durante el periodo de prueba ser&aacute;n las mismas que en nuestro servicio gratuito. Cuando se haga el lanzamiento del servicio Pro, habr&aacute;n nuevas caracter&iacute;sticas disponibles por una mensualidad.'),(306,'pt','NoteBeta','Nota: Estamos en etapa de BETA p&uacute;blico. Las caracter&iacute;sticas disponibles durante el periodo de prueba ser&aacute;n las mismas que en nuestro servicio gratuito. Cuando se haga el lanzamiento del servicio Pro, habr&aacute;n nuevas caracter&iacute;sticas disponibles por una mensualidad.'),(307,'en','EULA','<p><strong>S3MER.COM USER AGREEMENT - TERMS OF USE</strong></p>\n		<p>Updated as of MAY 1, 2008</p>\n		<p><strong>PLEASE READ THESE TERMS OF USE CAREFULLY BECAUSE THEY DESCRIBE YOUR RIGHTS AND RESPONSIBILITIES AND CONSTITUTE A LEGALLY BINDING AGREEMENT BETWEEN YOU AND S3MER REGARDING YOUR USE OF OUR WEBSITE AND SERVICES OFFERED.</strong></p>\n\n		<p>1. Your Acceptance</p>\n		<p>1.1 By using and/or visiting this website (collectively, including all content and functionality available through the s3mer.com domain name, the \"S3mer Website\", or \"Website\"), you signify your agreement to (1) these terms and conditions (the \"Terms of Service\"), and (2)s3mer\'s privacy notice, found at http://www.S3mer.com/privacy and incorporated here by reference. If you do not agree to any of these terms, the S3mer privacy notice or the Community Guidelines, please do not use the S3mer Website.</p>\n		<p>1.2 Although we may attempt to notify you when major changes are made to these Terms of Service, you should periodically review the most up-to-date version http://www.s3mer.com/terms. S3mer may, at its sole discretion, modify or revise these Terms of Service and policies at any time, and you agree to be bound by such modifications or revisions. Nothing in this Agreement shall be deemed to confer any third-party rights or benefits.</p>\n\n		<p>2. S3mer Website</p>\n	<p>	2.1 These Terms of Service apply to all users of the S3mer Website, including users who are also contributors of multimedia content, information, and other materials or services on the Website. The S3mer Website includes all aspects of S3mer, including but not limited to S3mer players, channels, shows, media, and the S3mer Player application.</p>\n	<p>	2.2 The S3mer Website may contain links to third party websites that are not owned or controlled by S3mer. S3mer has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party websites. In addition, S3mer will not and cannot censor or edit the content of any third-party site. By using the Website, you expressly relieve S3mer from any and all liability arising from your use of any third-party website.</p>\n		<p>2.3 Accordingly, we encourage you to be aware when you leave the S3mer Website and to read the terms and conditions and privacy policy of each other website that you visit.</p>\n\n		<p>3. S3mer Accounts</p>\n		<p>3.1 In order to access some features of the Website, you will have to create an S3mer account. You may never use another user\'s account without permission. When creating your account, you must provide accurate and complete information. You are solely responsible for the activity that occurs on your account, and you must keep your account password secure. You must notify S3mer immediately of any breach of security or unauthorized use of your account.</p>\n		<p>3.2 Although S3mer will not be liable for your losses caused by any unauthorized use of your account, you may be liable for the losses of S3mer or others due to such unauthorized use.</p>\n\n		<p>4. License Grant & Restrictions</p>\n		<p>4.1 S3mer hereby grants you a non-exclusive, non-transferable right to use the Service, solely for your own internal business purposes, subject to the terms and conditions of this Agreement.</p>\n	<p>	4.2 This service is intended to be used on PC-based machines only with full versions of operating systems. If you need to use our service for a non-PC based device such as mobile and other digital signage that uses an embedded operating system, please contact us for a non-flash based solution</p>\n		<p>4.3 You may not upload any content or data that is specifically designed to degrade, overload or stress any component of the Service offered by S3mer.</p>\n	<p>	4.4 You may only sign up for one free account. S3mer reserves the right to cancel any account that found to be a duplicate. By signing up for a free account, you automatically give permission to S3mer to insert ads at regular intervals for the promotion of S3mer digital signage free service or third parties. If you do not wish to display ads in your digital signage playback content, you must upgrade your subscription to Pro.</p>\n		<p>4.5 You may not access the Service if you are a direct or indirect competitor of S3mer, except with S3mer\'s prior written consent. In addition, you may not access the Service for purposes of monitoring its availability, performance or functionality, or for any other benchmarking or competitive purposes</p>\n		<p>4.6 Without S3mer\'s prior written consent, you shall not (i) license, sublicense, sell, resell, transfer, assign, distribute or otherwise commercially exploit or make available to any third party the Service or any part of the software content in any way; (ii) modify or make derivative works based upon the Service or any part of the software content; or (iii) reverse engineer or access the Service in order to (a) build a competitive product or service, (b) build a product using similar ideas, features, functions or graphics of the Service, or (c) copy any ideas, features, functions or graphics of the Service.</p>\n		<p>4.7 You may use the Service only for your internal business purposes and shall not: (i) send or store infringing, obscene, threatening, libelous, or otherwise unlawful material, including material harmful to children or infringe third party privacy rights; (ii) send or store material containing software viruses, worms, Trojan horses or other harmful computer code, files, scripts, agents or programs; (iii) interfere with or disrupt the integrity or performance of the Service or the data contained therein; or (iv) attempt to gain unauthorized access to the Service or its related systems or networks.</p>\n\n		<p>5. General Use of the Website ? Permissions and Restrictions\n		S3mer hereby grants you permission to access and use the Website as set forth in these Terms of Service, provided that:</p>\n		<p>5.1 You agree not to distribute in any medium any part of the Website, including but not limited to User Submissions (defined below), without S3mer\'s prior written authorization.</p>\n		<p>5.2 You agree not to alter or modify any part of the Website, including but not limited to S3mer\'s Player application or any of its related technologies.</p>\n		<p>5.3 You agree not to access User Submissions (defined below) or S3mer Content through any technology or means other than the multimedia playback pages of the Website itself, the S3mer Offline Player, or other explicitly authorized means S3mer may designate.</p>\n		<p>5.4 You agree not to use the Website, including the S3mer Player application for any commercial use, without the prior written authorization of S3mer. Prohibited commercial uses include any of the following actions taken without S3mer\'s express approval:</p>\n		<p>5.4.1 sale of access to the Website or its related services (such as the Player application) on another website;</p>\n		<p>5.4.2 and any use of the Website or its related services (such as Player application) that S3mer finds, in its sole discretion, to use S3mer\'s resources or User Submissions with the effect of competing with or displacing the market for S3mer, S3mer content, or its User Submissions.</p>\n		<p>5. Prohibited commercial uses do not include:</p>\n		<p>5.5.1 uploading an original video to S3mer, or maintaining an original show S3mer, to promote your business or artistic enterprise;</p>\n		<p>5.5.2 any use that S3mer expressly authorizes in writing.</p>\n		<p>5.6 If you use the S3mer website or Player application, you may not modify, build upon, or block any portion of the Player application in any way.</p>\n		<p>5.7 If you use the S3mer Player application, you agree that it may automatically download and install updates from time to time from S3mer. These updates are designed to improve, enhance and further develop the Player application and may take the form of bug fixes, enhanced functions, new software modules and completely new versions. You agree to receive such updates (and permit S3mer to deliver these to you) as part of your use of the Player application.</p>\n	<p>	5.8 You agree not to use or launch any automated system, including without limitation, \"robots,\" \"spiders,\" or \"offline readers,\" that accesses the Website in a manner that sends more request messages to the S3mer servers in a given period of time than a human can reasonably produce in the same period by using a conventional on-line web browser. Notwithstanding the foregoing, S3mer grants the operators of public search engines permission to use spiders to copy materials from the site for the sole purpose of and solely to the extent necessary for creating publicly available searchable indices of the materials, but not caches or archives of such materials. S3mer reserves the right to revoke these exceptions either generally or in specific cases. You agree not to collect or harvest any personally identifiable information, including account names, from the Website, nor to use the communication systems provided by the Website (e.g. comments, email) for any commercial solicitation purposes. You agree not to solicit, for commercial purposes, any users of the Website with respect to their User Submissions.</p>\n	<p>	5.9 In your use of the website, you will otherwise comply with the terms and conditions of these Terms of Service, S3mer Community Guidelines, and all applicable local, national, and international laws and regulations.</p>\n		<p>5.10 S3mer reserves the right to discontinue any aspect of the S3mer Website at any time.</p>\n\n		<p>6. Your Use of Content on the Site\n		In addition to the general restrictions above, the following restrictions and conditions apply specifically to your use of content on the S3mer Website.</p>\n		<p>6.1 The content on the S3mer Website, except all User Submissions (as defined below), including without limitation, the text, software, scripts, graphics, photos, sounds, music, videos, interactive features and the like (\"Content\") and the trademarks, service marks and logos contained therein (\"Marks\"), are owned by or licensed to S3mer, subject to copyright and other intellectual property rights under the law. Content on the Website is provided to you AS IS for your information and personal use only and may not be downloaded, copied, reproduced, distributed, transmitted, broadcast, displayed, sold, licensed, or otherwise exploited for any other purposes whatsoever without the prior written consent of the respective owners. S3mer reserves all rights not expressly granted in and to the Website and the Content.</p>\n		<p>6.2 You may access User Submissions solely:</p>\n		<p>6.2.1 for your information and personal use;</p>\n		<p>6.2.2 as intended through the normal functionality of the S3mer Service.</p>\n		<p>6.3 You may access S3mer Content, User Submissions and other content only as permitted under this Agreement. S3mer reserves all rights not expressly granted in and to the S3mer Content and the S3mer Service.</p>\n		<p>6.4 You agree to not engage in the use, copying, or distribution of any of the Content other than expressly permitted herein, including any use, copying, or distribution of User Submissions of third parties obtained through the Website for any commercial purposes.</p>\n		<p>6.5 You agree not to circumvent, disable or otherwise interfere with security-related features of the S3mer Website or features that prevent or restrict use or copying of any Content or enforce limitations on use of the S3mer Website or the Content therein.</p>\n		<p>6.6 You understand that when using the S3mer Website, you will be exposed to User Submissions from a variety of sources, and that S3mer is not responsible for the accuracy, usefulness, safety, or intellectual property rights of or relating to such User Submissions. You further understand and acknowledge that you may be exposed to User Submissions that are inaccurate, offensive, indecent, or objectionable, and you agree to waive, and hereby do waive, any legal or equitable rights or remedies you have or may have against S3mer with respect thereto, and agree to indemnify and hold S3mer, its Owners/Operators, affiliates, and/or licensors, harmless to the fullest extent allowed by law regarding all matters related to your use of the site.</p>\n		<p>6.7 Your User Submissions and Conduct</p>\n		<p>6.7.1 As an S3mer account holder you may submit multimedia content referred to as \"User Submissions.\" You understand that whether or not such User Submissions are published, S3mer does not guarantee any confidentiality with respect to any User Submissions.</p>\n		<p>6.7.2 You shall be solely responsible for your own User Submissions and the consequences of posting or publishing them. In connection with User Submissions, you affirm, represent, and/or warrant that: you own or have the necessary licenses, rights, consents, and permissions to use and authorize S3mer to use all patent, trademark, trade secret, copyright or other proprietary rights in and to any and all User Submissions to enable inclusion and use of the User Submissions in the manner contemplated by the Website and these Terms of Service.</p>\n		<p>6.3 For clarity, you retain all of your ownership rights in your User Submissions. However, by submitting User Submissions to S3mer, you hereby grant S3mer a worldwide, non-exclusive, royalty-free, sub-licensable and transferable license to use, reproduce, distribute, prepare derivative works of, display, and perform the User Submissions in connection with the S3mer Website and S3mer\'s (and its successors\' and affiliates\') business, including without limitation for promoting and redistributing part or all of the S3mer Website (and derivative works thereof) in any media formats and through any media channels.</p>\n		<p>6.4 In connection with User Submissions, you further agree that you will not submit material that is copyrighted, protected by trade secret or otherwise subject to third party proprietary rights, including privacy and publicity rights, unless you are the owner of such rights or have permission from their rightful owner to post the material and to grant S3mer all of the license rights granted herein.</p>\n		<p>6.5 You further agree that you will not, in connection with User Submissions, submit material that is contrary to the S3mer Content Guidelines, found as below, or contrary to applicable local, national, and international laws and regulations.</p>\n		<p>6.6 S3mer does not endorse any User Submission or any opinion, recommendation, or advice expressed therein, and S3mer expressly disclaims any and all liability in connection with User Submissions. S3mer does not permit copyright infringing activities and infringement of intellectual property rights on its Website, and S3mer will remove all Content and User Submissions if properly notified that such Content or User Submission infringes on another\'s intellectual property rights. S3mer reserves the right to remove Content and User Submissions without prior notice.</p>\n\n		<p>8. Account Termination Policy</p>\n		<p>8.1 S3mer will terminate a User\'s access to its Website if, under appropriate circumstances, they are determined to be an infringer.</p>\n		<p>8.2 S3mer reserves the right to decide whether Content or a User Submission is appropriate and complies with these Terms of Service for violations other than copyright infringement, such as, but not limited to, pornography, obscene or defamatory material, or excessive length. S3mer may remove such User Submissions and/or terminate a User\'s access for uploading such material in violation of these Terms of Service at any time, without prior notice and at its sole discretion.</p>\n\n		<p>9. Copyright Infringement</p>\n		<p>9.1 If you are a copyright owner or an agent thereof and believe that any User Submission or other content infringes upon your copyrights, you may submit a request to block your copyright content by providing us with the following information in writing</p>\n		<p>9.1.1 A physical or electronic signature of a person authorized to act on behalf of the owner of an exclusive right that is allegedly infringed;</p>\n		<p>9.1.2 Identification of the copyrighted work claimed to have been infringed, or, if multiple copyrighted works at a single online site are covered by a single notification, a representative list of such works at that site;</p>\n		<p>9.1.3 Identification of the material that is claimed to be infringing or to be the subject of infringing activity and that is to be removed or access to which is to be disabled and information reasonably sufficient to permit the service provider to locate the material;</p>\n		<p>9.1.4 Information reasonably sufficient to permit the service provider to contact you, such as an address, telephone number, and, if available, an electronic mail;</p>\n		<p>9.1.5 A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law; and</p>\n		<p>9.1.6 A statement that the information in the notification is accurate, and under penalty of perjury, that you are authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.</p>\n		<p>9.2 Counter-Notice. If you believe that your User Submission that was removed (or to which access was disabled) is not infringing, or that you have the authorization from the copyright owner, the copyright owner\'s agent, or pursuant to the law, to post and use the content in your User Submission, you may send a counter-notice containing the following information to us:</p>\n		<p>9.2.1 Your physical or electronic signature;</p>\n		<p>9.2.2 Identification of the content that has been removed or to which access has been disabled and the location at which the content appeared before it was removed or disabled;</p>\n		<p>9.2.3 A statement that you have a good faith belief that the content was removed or disabled as a result of mistake or a misidentification of the content; and</p>\n		<p>9.2.4 Your name, address, telephone number, and e-mail address, a statement that you consent to the jurisdiction of the High court in Indore, India, and a statement that you will accept service of process from the person who provided notification of the alleged infringement.</p>\n		<p>If a counter-notice is received by us, S3mer may send a copy of the counter-notice to the original complaining party informing that person that it may replace the removed content or cease disabling it in 10 business days. Unless the copyright owner files an action seeking a court order against the content provider, member or user, the removed content may be replaced, or access to it restored, in 10 to 14 business days or more after receipt of the counter-notice, at S3mer\'s sole discretion.</p>\n\n		<p>10. Warranty Disclaimer</p>\n		<p>YOU AGREE THAT YOUR USE OF THE S3MER WEBSITE SHALL BE AT YOUR SOLE RISK. TO THE FULLEST EXTENT PERMITTED BY LAW, S3MER, ITS OFFICERS, DIRECTORS, EMPLOYEES, AND AGENTS DISCLAIM ALL WARRANTIES, EXPRESS OR IMPLIED, IN CONNECTION WITH THE WEBSITE AND YOUR USE THEREOF. S3MER MAKES NO WARRANTIES OR REPRESENTATIONS ABOUT THE ACCURACY OR COMPLETENESS OF THIS SITE\'S CONTENT OR THE CONTENT OF ANY SITES LINKED TO THIS SITE AND ASSUMES NO LIABILITY OR RESPONSIBILITY FOR ANY (I) ERRORS, MISTAKES, OR INACCURACIES OF CONTENT, (II) PERSONAL INJURY OR PROPERTY DAMAGE, OF ANY NATURE WHATSOEVER, RESULTING FROM YOUR ACCESS TO AND USE OF OUR WEBSITE, (III) ANY UNAUTHORIZED ACCESS TO OR USE OF OUR SECURE SERVERS AND/OR ANY AND ALL PERSONAL INFORMATION AND/OR FINANCIAL INFORMATION STORED THEREIN, (IV) ANY INTERRUPTION OR CESSATION OF TRANSMISSION TO OR FROM OUR WEBSITE, (IV) ANY BUGS, VIRUSES, TROJAN HORSES, OR THE LIKE WHICH MAY BE TRANSMITTED TO OR THROUGH OUR WEBSITE BY ANY THIRD PARTY, AND/OR (V) ANY ERRORS OR OMISSIONS IN ANY CONTENT OR FOR ANY LOSS OR DAMAGE OF ANY KIND INCURRED AS A RESULT OF THE USE OF ANY CONTENT POSTED, EMAILED, TRANSMITTED, OR OTHERWISE MADE AVAILABLE VIA THE S3MER WEBSITE. S3MER DOES NOT WARRANT, ENDORSE, GUARANTEE, OR ASSUME RESPONSIBILITY FOR ANY PRODUCT OR SERVICE ADVERTISED OR OFFERED BY A THIRD PARTY THROUGH THE S3MER WEBSITE OR ANY HYPERLINKED WEBSITE OR FEATURED IN ANY BANNER OR OTHER ADVERTISING, AND S3MER WILL NOT BE A PARTY TO OR IN ANY WAY BE RESPONSIBLE FOR MONITORING ANY TRANSACTION BETWEEN YOU AND THIRD-PARTY PROVIDERS OF PRODUCTS OR SERVICES. AS WITH THE PURCHASE OF A PRODUCT OR SERVICE THROUGH ANY MEDIUM OR IN ANY ENVIRONMENT, YOU SHOULD USE YOUR BEST JUDGMENT AND EXERCISE CAUTION WHERE APPROPRIATE.</p>\n\n		<p>11. Limitation of Liability</p>\n		<p>IN NO EVENT SHALL S3MER, ITS OFFICERS, DIRECTORS, EMPLOYEES, OR AGENTS, BE LIABLE TO YOU FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, PUNITIVE, OR CONSEQUENTIAL DAMAGES WHATSOEVER RESULTING FROM ANY (I) ERRORS, MISTAKES, OR INACCURACIES OF CONTENT, (II) PERSONAL INJURY OR PROPERTY DAMAGE, OF ANY NATURE WHATSOEVER, RESULTING FROM YOUR ACCESS TO AND USE OF OUR WEBSITE, (III) ANY UNAUTHORIZED ACCESS TO OR USE OF OUR SECURE SERVERS AND/OR ANY AND ALL PERSONAL INFORMATION AND/OR FINANCIAL INFORMATION STORED THEREIN, (IV) ANY INTERRUPTION OR CESSATION OF TRANSMISSION TO OR FROM OUR WEBSITE, (IV) ANY BUGS, VIRUSES, TROJAN HORSES, OR THE LIKE, WHICH MAY BE TRANSMITTED TO OR THROUGH OUR WEBSITE BY ANY THIRD PARTY, AND/OR (V) ANY ERRORS OR OMISSIONS IN ANY CONTENT OR FOR ANY LOSS OR DAMAGE OF ANY KIND INCURRED AS A RESULT OF YOUR USE OF ANY CONTENT POSTED, EMAILED, TRANSMITTED, OR OTHERWISE MADE AVAILABLE VIA THE S3MER WEBSITE, WHETHER BASED ON WARRANTY, CONTRACT, TORT, OR ANY OTHER LEGAL THEORY, AND WHETHER OR NOT THE COMPANY IS ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. THE FOREGOING LIMITATION OF LIABILITY SHALL APPLY TO THE FULLEST EXTENT PERMITTED BY LAW IN THE APPLICABLE JURISDICTION.</p>\n		<p>YOU SPECIFICALLY ACKNOWLEDGE THAT S3MER SHALL NOT BE LIABLE FOR USER SUBMISSIONS OR THE DEFAMATORY, OFFENSIVE, OR ILLEGAL CONDUCT OF ANY THIRD PARTY AND THAT THE RISK OF HARM OR DAMAGE FROM THE FOREGOING RESTS ENTIRELY WITH YOU.</p>\n\n		<p>12. Indemnity</p>\n		<p>You agree to defend, indemnify and hold harmless S3mer, its parent corporation, officers, directors, employees and agents, from and against any and all claims, damages, obligations, losses, liabilities, costs or debt, and expenses (including but not limited to attorney\'s fees) arising from: (i) your use of and access to the S3mer Website; (ii) your violation of any term of these Terms of Service; (iii) your violation of any third party right, including without limitation any copyright, property, or privacy right; or (iv) any claim that one of your User Submissions caused damage to a third party. This defense and indemnification obligation will survive these Terms of Service and your use of the S3mer Website.</p>\n\n		<p>13. Ability to Accept Terms of Service</p>\n		<p>You affirm that you are either more than 18 years of age, or an emancipated minor, or possess legal parental or guardian consent, and are fully able and competent to enter into the terms, conditions, obligations, affirmations, representations, and warranties set forth in these Terms of Service, and to abide by and comply with these Terms of Service. In any case, you affirm that you are over the age of 13, as the S3mer Website is not intended for children under 13. If you are under 13 years of age, then please do not use the S3mer Website.</p> \n\n		<p>14. Assignment</p>\n		<p>These Terms of Service, and any rights and licenses granted hereunder, may not be transferred or assigned by you, but may be assigned by S3mer without restriction.</p>'),(308,'en','PrivacyPolicy','<p><strong>s3mer, Inc: Privacy Notice</strong></p>\n		<p>As of May 1, 2008</p>\n		<p><strong>Your privacy is important to s3mer, inc. Therefore, s3mer is committed to respecting your privacy and the confidentiality of your personal data and messaging content.</strong></p>\n		<p><strong>The following describes privacy practices specific to s3mer. To understand how we treat the information you give us as you use s3mer, you should read this policy.</strong></p>\n\n		<p>1. Personal Information</p>\n		<p>1.1 Non-Account Activity. You can watch multimedia content on s3mer without having an s3mer Account. You can also contact us about a particular content without having an s3mer Account.</p>\n		<p>1.2 Account-Related Activity. Certain activities on s3mer - like uploading videos, posting messages - require you to have an s3mer Account. We ask for some personal information when you create an s3mer Account, including your email address and a password, which is used to protect your account from unauthorized access.</p>\n		<p>1.3 Usage Information. We may record information about your usage, such as when you use s3mer, the services, components, and external data sources you subscribe to, the type of multimedia content you upload, the multimedia you play, and the frequency and size of data transfers, as well as information you display or click on in s3mer (including UI elements, settings, and other information). If you are logged in, we may associate that information with your account. We may use clear GIFs (a.k.a. \"Web Beacons\") in HTML-based emails sent to our users to track which emails are opened by recipients.</p>\n		<p>1.4 Content Uploaded to Site. Any personal information or multimedia content that you voluntarily disclose online (on discussion boards, in messages and chat areas, or within your playback, etc.) becomes publicly available and can be collected and used by others.</p>\n\n		<p>2. Uses</p>\n		<p>2.1 If you submit personally identifiable information to us through the s3mer Site, we use your personal information to operate, maintain, and provide to you the general and personalized features and functionality of the s3mer Site, and to process any correspondence you send to us.</p>\n		<p>2.2 Any multimedia content that you submit to the s3mer Site may be redistributed through the internet and other media channels, and may be viewed by the general public.</p>\n		<p>2.3 We do not use your email address or other personally identifiable information to send commercial or marketing messages without your consent or except as part of a specific program or feature for which you will have the ability to opt-in or opt-out. We may, however, use your email address without further consent for non-marketing or administrative purposes (such as notifying you of major S3mer Site changes or for customer service purposes).</p>\n		<p>2.4 We use both your personally identifiable information and certain non-personally-identifiable information (such as anonymous user usage data, cookies, IP addresses, browser type, click stream data, etc.) to improve the quality and design of the S3mer Site and to create new features, promotions, functionality, and services by storing, tracking, analyzing, and processing user preferences and trends, as well as user activity and communications.</p>\n		<p>2.5 We use cookies, clear gifs, and log file information to: (a) store information so that you will not have to re-enter it during your visit or the next time you visit the S3mer Site; (b) provide custom, personalized content and information; (c) monitor the effectiveness of our marketing campaigns; (d) monitor aggregate metrics such as total number of visitors, pages viewed, etc.; and (e) track your entries, submissions, and status in promotions, sweepstakes, and contests. If you download the S3mer Player application, your copy of Player includes a unique application number. The unique application number and information about your installation of the Player (e.g. version number, language) will be sent to S3mer when the Player automatically checks for updates.</p>\n\n		<p>3. Your Choices</p>\n		<p>3.1 You may, of course, decline to submit personally identifiable information through the s3mer Site, in which case s3mer may not be able to provide certain services to you. Some advanced s3mer features may, for authentication purposes, require you to sign up. The privacy notices of those services govern the use of your personal information associated with them.</p>\n		<p>3.2 You may update or correct your personal profile information and email preferences at any time by visiting your settings page.</p>\n\n		<p>4. More information\n		Notice: We may change this Privacy Policy as a result of a change of policy in our company. If you have any questions regarding our Privacy Statement, or any requests to correct or clarify your personally identifying information, or requests to modify your \"opt-out\" preferences can be directed to:</p>\n\n		<p>s3mer, inc.</p>\n		<p>529 Andaluc&iacute;a Ave.</p>\n		<p>San Juan, PR 00920</p>\n		<p>E-mail: privacy.policy@s3mer.com</p>'),(309,'es','EULA','<p><strong>S3MER.COM USER AGREEMENT - TERMS OF USE</strong></p>\n		<p>Updated as of MAY 1, 2008</p>\n		<p><strong>PLEASE READ THESE TERMS OF USE CAREFULLY BECAUSE THEY DESCRIBE YOUR RIGHTS AND RESPONSIBILITIES AND CONSTITUTE A LEGALLY BINDING AGREEMENT BETWEEN YOU AND S3MER REGARDING YOUR USE OF OUR WEBSITE AND SERVICES OFFERED.</strong></p>\n\n		<p>1. Your Acceptance</p>\n		<p>1.1 By using and/or visiting this website (collectively, including all content and functionality available through the s3mer.com domain name, the \"S3mer Website\", or \"Website\"), you signify your agreement to (1) these terms and conditions (the \"Terms of Service\"), and (2)s3mer\'s privacy notice, found at http://www.S3mer.com/privacy and incorporated here by reference. If you do not agree to any of these terms, the S3mer privacy notice or the Community Guidelines, please do not use the S3mer Website.</p>\n		<p>1.2 Although we may attempt to notify you when major changes are made to these Terms of Service, you should periodically review the most up-to-date version http://www.s3mer.com/terms. S3mer may, at its sole discretion, modify or revise these Terms of Service and policies at any time, and you agree to be bound by such modifications or revisions. Nothing in this Agreement shall be deemed to confer any third-party rights or benefits.</p>\n\n		<p>2. S3mer Website</p>\n	<p>	2.1 These Terms of Service apply to all users of the S3mer Website, including users who are also contributors of multimedia content, information, and other materials or services on the Website. The S3mer Website includes all aspects of S3mer, including but not limited to S3mer players, channels, shows, media, and the S3mer Player application.</p>\n	<p>	2.2 The S3mer Website may contain links to third party websites that are not owned or controlled by S3mer. S3mer has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party websites. In addition, S3mer will not and cannot censor or edit the content of any third-party site. By using the Website, you expressly relieve S3mer from any and all liability arising from your use of any third-party website.</p>\n		<p>2.3 Accordingly, we encourage you to be aware when you leave the S3mer Website and to read the terms and conditions and privacy policy of each other website that you visit.</p>\n\n		<p>3. S3mer Accounts</p>\n		<p>3.1 In order to access some features of the Website, you will have to create an S3mer account. You may never use another user\'s account without permission. When creating your account, you must provide accurate and complete information. You are solely responsible for the activity that occurs on your account, and you must keep your account password secure. You must notify S3mer immediately of any breach of security or unauthorized use of your account.</p>\n		<p>3.2 Although S3mer will not be liable for your losses caused by any unauthorized use of your account, you may be liable for the losses of S3mer or others due to such unauthorized use.</p>\n\n		<p>4. License Grant & Restrictions</p>\n		<p>4.1 S3mer hereby grants you a non-exclusive, non-transferable right to use the Service, solely for your own internal business purposes, subject to the terms and conditions of this Agreement.</p>\n	<p>	4.2 This service is intended to be used on PC-based machines only with full versions of operating systems. If you need to use our service for a non-PC based device such as mobile and other digital signage that uses an embedded operating system, please contact us for a non-flash based solution</p>\n		<p>4.3 You may not upload any content or data that is specifically designed to degrade, overload or stress any component of the Service offered by S3mer.</p>\n	<p>	4.4 You may only sign up for one free account. S3mer reserves the right to cancel any account that found to be a duplicate. By signing up for a free account, you automatically give permission to S3mer to insert ads at regular intervals for the promotion of S3mer digital signage free service or third parties. If you do not wish to display ads in your digital signage playback content, you must upgrade your subscription to Pro.</p>\n		<p>4.5 You may not access the Service if you are a direct or indirect competitor of S3mer, except with S3mer\'s prior written consent. In addition, you may not access the Service for purposes of monitoring its availability, performance or functionality, or for any other benchmarking or competitive purposes</p>\n		<p>4.6 Without S3mer\'s prior written consent, you shall not (i) license, sublicense, sell, resell, transfer, assign, distribute or otherwise commercially exploit or make available to any third party the Service or any part of the software content in any way; (ii) modify or make derivative works based upon the Service or any part of the software content; or (iii) reverse engineer or access the Service in order to (a) build a competitive product or service, (b) build a product using similar ideas, features, functions or graphics of the Service, or (c) copy any ideas, features, functions or graphics of the Service.</p>\n		<p>4.7 You may use the Service only for your internal business purposes and shall not: (i) send or store infringing, obscene, threatening, libelous, or otherwise unlawful material, including material harmful to children or infringe third party privacy rights; (ii) send or store material containing software viruses, worms, Trojan horses or other harmful computer code, files, scripts, agents or programs; (iii) interfere with or disrupt the integrity or performance of the Service or the data contained therein; or (iv) attempt to gain unauthorized access to the Service or its related systems or networks.</p>\n\n		<p>5. General Use of the Website ? Permissions and Restrictions\n		S3mer hereby grants you permission to access and use the Website as set forth in these Terms of Service, provided that:</p>\n		<p>5.1 You agree not to distribute in any medium any part of the Website, including but not limited to User Submissions (defined below), without S3mer\'s prior written authorization.</p>\n		<p>5.2 You agree not to alter or modify any part of the Website, including but not limited to S3mer\'s Player application or any of its related technologies.</p>\n		<p>5.3 You agree not to access User Submissions (defined below) or S3mer Content through any technology or means other than the multimedia playback pages of the Website itself, the S3mer Offline Player, or other explicitly authorized means S3mer may designate.</p>\n		<p>5.4 You agree not to use the Website, including the S3mer Player application for any commercial use, without the prior written authorization of S3mer. Prohibited commercial uses include any of the following actions taken without S3mer\'s express approval:</p>\n		<p>5.4.1 sale of access to the Website or its related services (such as the Player application) on another website;</p>\n		<p>5.4.2 and any use of the Website or its related services (such as Player application) that S3mer finds, in its sole discretion, to use S3mer\'s resources or User Submissions with the effect of competing with or displacing the market for S3mer, S3mer content, or its User Submissions.</p>\n		<p>5. Prohibited commercial uses do not include:</p>\n		<p>5.5.1 uploading an original video to S3mer, or maintaining an original show S3mer, to promote your business or artistic enterprise;</p>\n		<p>5.5.2 any use that S3mer expressly authorizes in writing.</p>\n		<p>5.6 If you use the S3mer website or Player application, you may not modify, build upon, or block any portion of the Player application in any way.</p>\n		<p>5.7 If you use the S3mer Player application, you agree that it may automatically download and install updates from time to time from S3mer. These updates are designed to improve, enhance and further develop the Player application and may take the form of bug fixes, enhanced functions, new software modules and completely new versions. You agree to receive such updates (and permit S3mer to deliver these to you) as part of your use of the Player application.</p>\n	<p>	5.8 You agree not to use or launch any automated system, including without limitation, \"robots,\" \"spiders,\" or \"offline readers,\" that accesses the Website in a manner that sends more request messages to the S3mer servers in a given period of time than a human can reasonably produce in the same period by using a conventional on-line web browser. Notwithstanding the foregoing, S3mer grants the operators of public search engines permission to use spiders to copy materials from the site for the sole purpose of and solely to the extent necessary for creating publicly available searchable indices of the materials, but not caches or archives of such materials. S3mer reserves the right to revoke these exceptions either generally or in specific cases. You agree not to collect or harvest any personally identifiable information, including account names, from the Website, nor to use the communication systems provided by the Website (e.g. comments, email) for any commercial solicitation purposes. You agree not to solicit, for commercial purposes, any users of the Website with respect to their User Submissions.</p>\n	<p>	5.9 In your use of the website, you will otherwise comply with the terms and conditions of these Terms of Service, S3mer Community Guidelines, and all applicable local, national, and international laws and regulations.</p>\n		<p>5.10 S3mer reserves the right to discontinue any aspect of the S3mer Website at any time.</p>\n\n		<p>6. Your Use of Content on the Site\n		In addition to the general restrictions above, the following restrictions and conditions apply specifically to your use of content on the S3mer Website.</p>\n		<p>6.1 The content on the S3mer Website, except all User Submissions (as defined below), including without limitation, the text, software, scripts, graphics, photos, sounds, music, videos, interactive features and the like (\"Content\") and the trademarks, service marks and logos contained therein (\"Marks\"), are owned by or licensed to S3mer, subject to copyright and other intellectual property rights under the law. Content on the Website is provided to you AS IS for your information and personal use only and may not be downloaded, copied, reproduced, distributed, transmitted, broadcast, displayed, sold, licensed, or otherwise exploited for any other purposes whatsoever without the prior written consent of the respective owners. S3mer reserves all rights not expressly granted in and to the Website and the Content.</p>\n		<p>6.2 You may access User Submissions solely:</p>\n		<p>6.2.1 for your information and personal use;</p>\n		<p>6.2.2 as intended through the normal functionality of the S3mer Service.</p>\n		<p>6.3 You may access S3mer Content, User Submissions and other content only as permitted under this Agreement. S3mer reserves all rights not expressly granted in and to the S3mer Content and the S3mer Service.</p>\n		<p>6.4 You agree to not engage in the use, copying, or distribution of any of the Content other than expressly permitted herein, including any use, copying, or distribution of User Submissions of third parties obtained through the Website for any commercial purposes.</p>\n		<p>6.5 You agree not to circumvent, disable or otherwise interfere with security-related features of the S3mer Website or features that prevent or restrict use or copying of any Content or enforce limitations on use of the S3mer Website or the Content therein.</p>\n		<p>6.6 You understand that when using the S3mer Website, you will be exposed to User Submissions from a variety of sources, and that S3mer is not responsible for the accuracy, usefulness, safety, or intellectual property rights of or relating to such User Submissions. You further understand and acknowledge that you may be exposed to User Submissions that are inaccurate, offensive, indecent, or objectionable, and you agree to waive, and hereby do waive, any legal or equitable rights or remedies you have or may have against S3mer with respect thereto, and agree to indemnify and hold S3mer, its Owners/Operators, affiliates, and/or licensors, harmless to the fullest extent allowed by law regarding all matters related to your use of the site.</p>\n		<p>6.7 Your User Submissions and Conduct</p>\n		<p>6.7.1 As an S3mer account holder you may submit multimedia content referred to as \"User Submissions.\" You understand that whether or not such User Submissions are published, S3mer does not guarantee any confidentiality with respect to any User Submissions.</p>\n		<p>6.7.2 You shall be solely responsible for your own User Submissions and the consequences of posting or publishing them. In connection with User Submissions, you affirm, represent, and/or warrant that: you own or have the necessary licenses, rights, consents, and permissions to use and authorize S3mer to use all patent, trademark, trade secret, copyright or other proprietary rights in and to any and all User Submissions to enable inclusion and use of the User Submissions in the manner contemplated by the Website and these Terms of Service.</p>\n		<p>6.3 For clarity, you retain all of your ownership rights in your User Submissions. However, by submitting User Submissions to S3mer, you hereby grant S3mer a worldwide, non-exclusive, royalty-free, sub-licensable and transferable license to use, reproduce, distribute, prepare derivative works of, display, and perform the User Submissions in connection with the S3mer Website and S3mer\'s (and its successors\' and affiliates\') business, including without limitation for promoting and redistributing part or all of the S3mer Website (and derivative works thereof) in any media formats and through any media channels.</p>\n		<p>6.4 In connection with User Submissions, you further agree that you will not submit material that is copyrighted, protected by trade secret or otherwise subject to third party proprietary rights, including privacy and publicity rights, unless you are the owner of such rights or have permission from their rightful owner to post the material and to grant S3mer all of the license rights granted herein.</p>\n		<p>6.5 You further agree that you will not, in connection with User Submissions, submit material that is contrary to the S3mer Content Guidelines, found as below, or contrary to applicable local, national, and international laws and regulations.</p>\n		<p>6.6 S3mer does not endorse any User Submission or any opinion, recommendation, or advice expressed therein, and S3mer expressly disclaims any and all liability in connection with User Submissions. S3mer does not permit copyright infringing activities and infringement of intellectual property rights on its Website, and S3mer will remove all Content and User Submissions if properly notified that such Content or User Submission infringes on another\'s intellectual property rights. S3mer reserves the right to remove Content and User Submissions without prior notice.</p>\n\n		<p>8. Account Termination Policy</p>\n		<p>8.1 S3mer will terminate a User\'s access to its Website if, under appropriate circumstances, they are determined to be an infringer.</p>\n		<p>8.2 S3mer reserves the right to decide whether Content or a User Submission is appropriate and complies with these Terms of Service for violations other than copyright infringement, such as, but not limited to, pornography, obscene or defamatory material, or excessive length. S3mer may remove such User Submissions and/or terminate a User\'s access for uploading such material in violation of these Terms of Service at any time, without prior notice and at its sole discretion.</p>\n\n		<p>9. Copyright Infringement</p>\n		<p>9.1 If you are a copyright owner or an agent thereof and believe that any User Submission or other content infringes upon your copyrights, you may submit a request to block your copyright content by providing us with the following information in writing</p>\n		<p>9.1.1 A physical or electronic signature of a person authorized to act on behalf of the owner of an exclusive right that is allegedly infringed;</p>\n		<p>9.1.2 Identification of the copyrighted work claimed to have been infringed, or, if multiple copyrighted works at a single online site are covered by a single notification, a representative list of such works at that site;</p>\n		<p>9.1.3 Identification of the material that is claimed to be infringing or to be the subject of infringing activity and that is to be removed or access to which is to be disabled and information reasonably sufficient to permit the service provider to locate the material;</p>\n		<p>9.1.4 Information reasonably sufficient to permit the service provider to contact you, such as an address, telephone number, and, if available, an electronic mail;</p>\n		<p>9.1.5 A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law; and</p>\n		<p>9.1.6 A statement that the information in the notification is accurate, and under penalty of perjury, that you are authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.</p>\n		<p>9.2 Counter-Notice. If you believe that your User Submission that was removed (or to which access was disabled) is not infringing, or that you have the authorization from the copyright owner, the copyright owner\'s agent, or pursuant to the law, to post and use the content in your User Submission, you may send a counter-notice containing the following information to us:</p>\n		<p>9.2.1 Your physical or electronic signature;</p>\n		<p>9.2.2 Identification of the content that has been removed or to which access has been disabled and the location at which the content appeared before it was removed or disabled;</p>\n		<p>9.2.3 A statement that you have a good faith belief that the content was removed or disabled as a result of mistake or a misidentification of the content; and</p>\n		<p>9.2.4 Your name, address, telephone number, and e-mail address, a statement that you consent to the jurisdiction of the High court in Indore, India, and a statement that you will accept service of process from the person who provided notification of the alleged infringement.</p>\n		<p>If a counter-notice is received by us, S3mer may send a copy of the counter-notice to the original complaining party informing that person that it may replace the removed content or cease disabling it in 10 business days. Unless the copyright owner files an action seeking a court order against the content provider, member or user, the removed content may be replaced, or access to it restored, in 10 to 14 business days or more after receipt of the counter-notice, at S3mer\'s sole discretion.</p>\n\n		<p>10. Warranty Disclaimer</p>\n		<p>YOU AGREE THAT YOUR USE OF THE S3MER WEBSITE SHALL BE AT YOUR SOLE RISK. TO THE FULLEST EXTENT PERMITTED BY LAW, S3MER, ITS OFFICERS, DIRECTORS, EMPLOYEES, AND AGENTS DISCLAIM ALL WARRANTIES, EXPRESS OR IMPLIED, IN CONNECTION WITH THE WEBSITE AND YOUR USE THEREOF. S3MER MAKES NO WARRANTIES OR REPRESENTATIONS ABOUT THE ACCURACY OR COMPLETENESS OF THIS SITE\'S CONTENT OR THE CONTENT OF ANY SITES LINKED TO THIS SITE AND ASSUMES NO LIABILITY OR RESPONSIBILITY FOR ANY (I) ERRORS, MISTAKES, OR INACCURACIES OF CONTENT, (II) PERSONAL INJURY OR PROPERTY DAMAGE, OF ANY NATURE WHATSOEVER, RESULTING FROM YOUR ACCESS TO AND USE OF OUR WEBSITE, (III) ANY UNAUTHORIZED ACCESS TO OR USE OF OUR SECURE SERVERS AND/OR ANY AND ALL PERSONAL INFORMATION AND/OR FINANCIAL INFORMATION STORED THEREIN, (IV) ANY INTERRUPTION OR CESSATION OF TRANSMISSION TO OR FROM OUR WEBSITE, (IV) ANY BUGS, VIRUSES, TROJAN HORSES, OR THE LIKE WHICH MAY BE TRANSMITTED TO OR THROUGH OUR WEBSITE BY ANY THIRD PARTY, AND/OR (V) ANY ERRORS OR OMISSIONS IN ANY CONTENT OR FOR ANY LOSS OR DAMAGE OF ANY KIND INCURRED AS A RESULT OF THE USE OF ANY CONTENT POSTED, EMAILED, TRANSMITTED, OR OTHERWISE MADE AVAILABLE VIA THE S3MER WEBSITE. S3MER DOES NOT WARRANT, ENDORSE, GUARANTEE, OR ASSUME RESPONSIBILITY FOR ANY PRODUCT OR SERVICE ADVERTISED OR OFFERED BY A THIRD PARTY THROUGH THE S3MER WEBSITE OR ANY HYPERLINKED WEBSITE OR FEATURED IN ANY BANNER OR OTHER ADVERTISING, AND S3MER WILL NOT BE A PARTY TO OR IN ANY WAY BE RESPONSIBLE FOR MONITORING ANY TRANSACTION BETWEEN YOU AND THIRD-PARTY PROVIDERS OF PRODUCTS OR SERVICES. AS WITH THE PURCHASE OF A PRODUCT OR SERVICE THROUGH ANY MEDIUM OR IN ANY ENVIRONMENT, YOU SHOULD USE YOUR BEST JUDGMENT AND EXERCISE CAUTION WHERE APPROPRIATE.</p>\n\n		<p>11. Limitation of Liability</p>\n		<p>IN NO EVENT SHALL S3MER, ITS OFFICERS, DIRECTORS, EMPLOYEES, OR AGENTS, BE LIABLE TO YOU FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, PUNITIVE, OR CONSEQUENTIAL DAMAGES WHATSOEVER RESULTING FROM ANY (I) ERRORS, MISTAKES, OR INACCURACIES OF CONTENT, (II) PERSONAL INJURY OR PROPERTY DAMAGE, OF ANY NATURE WHATSOEVER, RESULTING FROM YOUR ACCESS TO AND USE OF OUR WEBSITE, (III) ANY UNAUTHORIZED ACCESS TO OR USE OF OUR SECURE SERVERS AND/OR ANY AND ALL PERSONAL INFORMATION AND/OR FINANCIAL INFORMATION STORED THEREIN, (IV) ANY INTERRUPTION OR CESSATION OF TRANSMISSION TO OR FROM OUR WEBSITE, (IV) ANY BUGS, VIRUSES, TROJAN HORSES, OR THE LIKE, WHICH MAY BE TRANSMITTED TO OR THROUGH OUR WEBSITE BY ANY THIRD PARTY, AND/OR (V) ANY ERRORS OR OMISSIONS IN ANY CONTENT OR FOR ANY LOSS OR DAMAGE OF ANY KIND INCURRED AS A RESULT OF YOUR USE OF ANY CONTENT POSTED, EMAILED, TRANSMITTED, OR OTHERWISE MADE AVAILABLE VIA THE S3MER WEBSITE, WHETHER BASED ON WARRANTY, CONTRACT, TORT, OR ANY OTHER LEGAL THEORY, AND WHETHER OR NOT THE COMPANY IS ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. THE FOREGOING LIMITATION OF LIABILITY SHALL APPLY TO THE FULLEST EXTENT PERMITTED BY LAW IN THE APPLICABLE JURISDICTION.</p>\n		<p>YOU SPECIFICALLY ACKNOWLEDGE THAT S3MER SHALL NOT BE LIABLE FOR USER SUBMISSIONS OR THE DEFAMATORY, OFFENSIVE, OR ILLEGAL CONDUCT OF ANY THIRD PARTY AND THAT THE RISK OF HARM OR DAMAGE FROM THE FOREGOING RESTS ENTIRELY WITH YOU.</p>\n\n		<p>12. Indemnity</p>\n		<p>You agree to defend, indemnify and hold harmless S3mer, its parent corporation, officers, directors, employees and agents, from and against any and all claims, damages, obligations, losses, liabilities, costs or debt, and expenses (including but not limited to attorney\'s fees) arising from: (i) your use of and access to the S3mer Website; (ii) your violation of any term of these Terms of Service; (iii) your violation of any third party right, including without limitation any copyright, property, or privacy right; or (iv) any claim that one of your User Submissions caused damage to a third party. This defense and indemnification obligation will survive these Terms of Service and your use of the S3mer Website.</p>\n\n		<p>13. Ability to Accept Terms of Service</p>\n		<p>You affirm that you are either more than 18 years of age, or an emancipated minor, or possess legal parental or guardian consent, and are fully able and competent to enter into the terms, conditions, obligations, affirmations, representations, and warranties set forth in these Terms of Service, and to abide by and comply with these Terms of Service. In any case, you affirm that you are over the age of 13, as the S3mer Website is not intended for children under 13. If you are under 13 years of age, then please do not use the S3mer Website.</p> \n\n		<p>14. Assignment</p>\n		<p>These Terms of Service, and any rights and licenses granted hereunder, may not be transferred or assigned by you, but may be assigned by S3mer without restriction.</p>'),(310,'pt','EULA','<p><strong>S3MER.COM USER AGREEMENT - TERMS OF USE</strong></p>\n		<p>Updated as of MAY 1, 2008</p>\n		<p><strong>PLEASE READ THESE TERMS OF USE CAREFULLY BECAUSE THEY DESCRIBE YOUR RIGHTS AND RESPONSIBILITIES AND CONSTITUTE A LEGALLY BINDING AGREEMENT BETWEEN YOU AND S3MER REGARDING YOUR USE OF OUR WEBSITE AND SERVICES OFFERED.</strong></p>\n\n		<p>1. Your Acceptance</p>\n		<p>1.1 By using and/or visiting this website (collectively, including all content and functionality available through the s3mer.com domain name, the \"S3mer Website\", or \"Website\"), you signify your agreement to (1) these terms and conditions (the \"Terms of Service\"), and (2)s3mer\'s privacy notice, found at http://www.S3mer.com/privacy and incorporated here by reference. If you do not agree to any of these terms, the S3mer privacy notice or the Community Guidelines, please do not use the S3mer Website.</p>\n		<p>1.2 Although we may attempt to notify you when major changes are made to these Terms of Service, you should periodically review the most up-to-date version http://www.s3mer.com/terms. S3mer may, at its sole discretion, modify or revise these Terms of Service and policies at any time, and you agree to be bound by such modifications or revisions. Nothing in this Agreement shall be deemed to confer any third-party rights or benefits.</p>\n\n		<p>2. S3mer Website</p>\n	<p>	2.1 These Terms of Service apply to all users of the S3mer Website, including users who are also contributors of multimedia content, information, and other materials or services on the Website. The S3mer Website includes all aspects of S3mer, including but not limited to S3mer players, channels, shows, media, and the S3mer Player application.</p>\n	<p>	2.2 The S3mer Website may contain links to third party websites that are not owned or controlled by S3mer. S3mer has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party websites. In addition, S3mer will not and cannot censor or edit the content of any third-party site. By using the Website, you expressly relieve S3mer from any and all liability arising from your use of any third-party website.</p>\n		<p>2.3 Accordingly, we encourage you to be aware when you leave the S3mer Website and to read the terms and conditions and privacy policy of each other website that you visit.</p>\n\n		<p>3. S3mer Accounts</p>\n		<p>3.1 In order to access some features of the Website, you will have to create an S3mer account. You may never use another user\'s account without permission. When creating your account, you must provide accurate and complete information. You are solely responsible for the activity that occurs on your account, and you must keep your account password secure. You must notify S3mer immediately of any breach of security or unauthorized use of your account.</p>\n		<p>3.2 Although S3mer will not be liable for your losses caused by any unauthorized use of your account, you may be liable for the losses of S3mer or others due to such unauthorized use.</p>\n\n		<p>4. License Grant & Restrictions</p>\n		<p>4.1 S3mer hereby grants you a non-exclusive, non-transferable right to use the Service, solely for your own internal business purposes, subject to the terms and conditions of this Agreement.</p>\n	<p>	4.2 This service is intended to be used on PC-based machines only with full versions of operating systems. If you need to use our service for a non-PC based device such as mobile and other digital signage that uses an embedded operating system, please contact us for a non-flash based solution</p>\n		<p>4.3 You may not upload any content or data that is specifically designed to degrade, overload or stress any component of the Service offered by S3mer.</p>\n	<p>	4.4 You may only sign up for one free account. S3mer reserves the right to cancel any account that found to be a duplicate. By signing up for a free account, you automatically give permission to S3mer to insert ads at regular intervals for the promotion of S3mer digital signage free service or third parties. If you do not wish to display ads in your digital signage playback content, you must upgrade your subscription to Pro.</p>\n		<p>4.5 You may not access the Service if you are a direct or indirect competitor of S3mer, except with S3mer\'s prior written consent. In addition, you may not access the Service for purposes of monitoring its availability, performance or functionality, or for any other benchmarking or competitive purposes</p>\n		<p>4.6 Without S3mer\'s prior written consent, you shall not (i) license, sublicense, sell, resell, transfer, assign, distribute or otherwise commercially exploit or make available to any third party the Service or any part of the software content in any way; (ii) modify or make derivative works based upon the Service or any part of the software content; or (iii) reverse engineer or access the Service in order to (a) build a competitive product or service, (b) build a product using similar ideas, features, functions or graphics of the Service, or (c) copy any ideas, features, functions or graphics of the Service.</p>\n		<p>4.7 You may use the Service only for your internal business purposes and shall not: (i) send or store infringing, obscene, threatening, libelous, or otherwise unlawful material, including material harmful to children or infringe third party privacy rights; (ii) send or store material containing software viruses, worms, Trojan horses or other harmful computer code, files, scripts, agents or programs; (iii) interfere with or disrupt the integrity or performance of the Service or the data contained therein; or (iv) attempt to gain unauthorized access to the Service or its related systems or networks.</p>\n\n		<p>5. General Use of the Website ? Permissions and Restrictions\n		S3mer hereby grants you permission to access and use the Website as set forth in these Terms of Service, provided that:</p>\n		<p>5.1 You agree not to distribute in any medium any part of the Website, including but not limited to User Submissions (defined below), without S3mer\'s prior written authorization.</p>\n		<p>5.2 You agree not to alter or modify any part of the Website, including but not limited to S3mer\'s Player application or any of its related technologies.</p>\n		<p>5.3 You agree not to access User Submissions (defined below) or S3mer Content through any technology or means other than the multimedia playback pages of the Website itself, the S3mer Offline Player, or other explicitly authorized means S3mer may designate.</p>\n		<p>5.4 You agree not to use the Website, including the S3mer Player application for any commercial use, without the prior written authorization of S3mer. Prohibited commercial uses include any of the following actions taken without S3mer\'s express approval:</p>\n		<p>5.4.1 sale of access to the Website or its related services (such as the Player application) on another website;</p>\n		<p>5.4.2 and any use of the Website or its related services (such as Player application) that S3mer finds, in its sole discretion, to use S3mer\'s resources or User Submissions with the effect of competing with or displacing the market for S3mer, S3mer content, or its User Submissions.</p>\n		<p>5. Prohibited commercial uses do not include:</p>\n		<p>5.5.1 uploading an original video to S3mer, or maintaining an original show S3mer, to promote your business or artistic enterprise;</p>\n		<p>5.5.2 any use that S3mer expressly authorizes in writing.</p>\n		<p>5.6 If you use the S3mer website or Player application, you may not modify, build upon, or block any portion of the Player application in any way.</p>\n		<p>5.7 If you use the S3mer Player application, you agree that it may automatically download and install updates from time to time from S3mer. These updates are designed to improve, enhance and further develop the Player application and may take the form of bug fixes, enhanced functions, new software modules and completely new versions. You agree to receive such updates (and permit S3mer to deliver these to you) as part of your use of the Player application.</p>\n	<p>	5.8 You agree not to use or launch any automated system, including without limitation, \"robots,\" \"spiders,\" or \"offline readers,\" that accesses the Website in a manner that sends more request messages to the S3mer servers in a given period of time than a human can reasonably produce in the same period by using a conventional on-line web browser. Notwithstanding the foregoing, S3mer grants the operators of public search engines permission to use spiders to copy materials from the site for the sole purpose of and solely to the extent necessary for creating publicly available searchable indices of the materials, but not caches or archives of such materials. S3mer reserves the right to revoke these exceptions either generally or in specific cases. You agree not to collect or harvest any personally identifiable information, including account names, from the Website, nor to use the communication systems provided by the Website (e.g. comments, email) for any commercial solicitation purposes. You agree not to solicit, for commercial purposes, any users of the Website with respect to their User Submissions.</p>\n	<p>	5.9 In your use of the website, you will otherwise comply with the terms and conditions of these Terms of Service, S3mer Community Guidelines, and all applicable local, national, and international laws and regulations.</p>\n		<p>5.10 S3mer reserves the right to discontinue any aspect of the S3mer Website at any time.</p>\n\n		<p>6. Your Use of Content on the Site\n		In addition to the general restrictions above, the following restrictions and conditions apply specifically to your use of content on the S3mer Website.</p>\n		<p>6.1 The content on the S3mer Website, except all User Submissions (as defined below), including without limitation, the text, software, scripts, graphics, photos, sounds, music, videos, interactive features and the like (\"Content\") and the trademarks, service marks and logos contained therein (\"Marks\"), are owned by or licensed to S3mer, subject to copyright and other intellectual property rights under the law. Content on the Website is provided to you AS IS for your information and personal use only and may not be downloaded, copied, reproduced, distributed, transmitted, broadcast, displayed, sold, licensed, or otherwise exploited for any other purposes whatsoever without the prior written consent of the respective owners. S3mer reserves all rights not expressly granted in and to the Website and the Content.</p>\n		<p>6.2 You may access User Submissions solely:</p>\n		<p>6.2.1 for your information and personal use;</p>\n		<p>6.2.2 as intended through the normal functionality of the S3mer Service.</p>\n		<p>6.3 You may access S3mer Content, User Submissions and other content only as permitted under this Agreement. S3mer reserves all rights not expressly granted in and to the S3mer Content and the S3mer Service.</p>\n		<p>6.4 You agree to not engage in the use, copying, or distribution of any of the Content other than expressly permitted herein, including any use, copying, or distribution of User Submissions of third parties obtained through the Website for any commercial purposes.</p>\n		<p>6.5 You agree not to circumvent, disable or otherwise interfere with security-related features of the S3mer Website or features that prevent or restrict use or copying of any Content or enforce limitations on use of the S3mer Website or the Content therein.</p>\n		<p>6.6 You understand that when using the S3mer Website, you will be exposed to User Submissions from a variety of sources, and that S3mer is not responsible for the accuracy, usefulness, safety, or intellectual property rights of or relating to such User Submissions. You further understand and acknowledge that you may be exposed to User Submissions that are inaccurate, offensive, indecent, or objectionable, and you agree to waive, and hereby do waive, any legal or equitable rights or remedies you have or may have against S3mer with respect thereto, and agree to indemnify and hold S3mer, its Owners/Operators, affiliates, and/or licensors, harmless to the fullest extent allowed by law regarding all matters related to your use of the site.</p>\n		<p>6.7 Your User Submissions and Conduct</p>\n		<p>6.7.1 As an S3mer account holder you may submit multimedia content referred to as \"User Submissions.\" You understand that whether or not such User Submissions are published, S3mer does not guarantee any confidentiality with respect to any User Submissions.</p>\n		<p>6.7.2 You shall be solely responsible for your own User Submissions and the consequences of posting or publishing them. In connection with User Submissions, you affirm, represent, and/or warrant that: you own or have the necessary licenses, rights, consents, and permissions to use and authorize S3mer to use all patent, trademark, trade secret, copyright or other proprietary rights in and to any and all User Submissions to enable inclusion and use of the User Submissions in the manner contemplated by the Website and these Terms of Service.</p>\n		<p>6.3 For clarity, you retain all of your ownership rights in your User Submissions. However, by submitting User Submissions to S3mer, you hereby grant S3mer a worldwide, non-exclusive, royalty-free, sub-licensable and transferable license to use, reproduce, distribute, prepare derivative works of, display, and perform the User Submissions in connection with the S3mer Website and S3mer\'s (and its successors\' and affiliates\') business, including without limitation for promoting and redistributing part or all of the S3mer Website (and derivative works thereof) in any media formats and through any media channels.</p>\n		<p>6.4 In connection with User Submissions, you further agree that you will not submit material that is copyrighted, protected by trade secret or otherwise subject to third party proprietary rights, including privacy and publicity rights, unless you are the owner of such rights or have permission from their rightful owner to post the material and to grant S3mer all of the license rights granted herein.</p>\n		<p>6.5 You further agree that you will not, in connection with User Submissions, submit material that is contrary to the S3mer Content Guidelines, found as below, or contrary to applicable local, national, and international laws and regulations.</p>\n		<p>6.6 S3mer does not endorse any User Submission or any opinion, recommendation, or advice expressed therein, and S3mer expressly disclaims any and all liability in connection with User Submissions. S3mer does not permit copyright infringing activities and infringement of intellectual property rights on its Website, and S3mer will remove all Content and User Submissions if properly notified that such Content or User Submission infringes on another\'s intellectual property rights. S3mer reserves the right to remove Content and User Submissions without prior notice.</p>\n\n		<p>8. Account Termination Policy</p>\n		<p>8.1 S3mer will terminate a User\'s access to its Website if, under appropriate circumstances, they are determined to be an infringer.</p>\n		<p>8.2 S3mer reserves the right to decide whether Content or a User Submission is appropriate and complies with these Terms of Service for violations other than copyright infringement, such as, but not limited to, pornography, obscene or defamatory material, or excessive length. S3mer may remove such User Submissions and/or terminate a User\'s access for uploading such material in violation of these Terms of Service at any time, without prior notice and at its sole discretion.</p>\n\n		<p>9. Copyright Infringement</p>\n		<p>9.1 If you are a copyright owner or an agent thereof and believe that any User Submission or other content infringes upon your copyrights, you may submit a request to block your copyright content by providing us with the following information in writing</p>\n		<p>9.1.1 A physical or electronic signature of a person authorized to act on behalf of the owner of an exclusive right that is allegedly infringed;</p>\n		<p>9.1.2 Identification of the copyrighted work claimed to have been infringed, or, if multiple copyrighted works at a single online site are covered by a single notification, a representative list of such works at that site;</p>\n		<p>9.1.3 Identification of the material that is claimed to be infringing or to be the subject of infringing activity and that is to be removed or access to which is to be disabled and information reasonably sufficient to permit the service provider to locate the material;</p>\n		<p>9.1.4 Information reasonably sufficient to permit the service provider to contact you, such as an address, telephone number, and, if available, an electronic mail;</p>\n		<p>9.1.5 A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law; and</p>\n		<p>9.1.6 A statement that the information in the notification is accurate, and under penalty of perjury, that you are authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.</p>\n		<p>9.2 Counter-Notice. If you believe that your User Submission that was removed (or to which access was disabled) is not infringing, or that you have the authorization from the copyright owner, the copyright owner\'s agent, or pursuant to the law, to post and use the content in your User Submission, you may send a counter-notice containing the following information to us:</p>\n		<p>9.2.1 Your physical or electronic signature;</p>\n		<p>9.2.2 Identification of the content that has been removed or to which access has been disabled and the location at which the content appeared before it was removed or disabled;</p>\n		<p>9.2.3 A statement that you have a good faith belief that the content was removed or disabled as a result of mistake or a misidentification of the content; and</p>\n		<p>9.2.4 Your name, address, telephone number, and e-mail address, a statement that you consent to the jurisdiction of the High court in Indore, India, and a statement that you will accept service of process from the person who provided notification of the alleged infringement.</p>\n		<p>If a counter-notice is received by us, S3mer may send a copy of the counter-notice to the original complaining party informing that person that it may replace the removed content or cease disabling it in 10 business days. Unless the copyright owner files an action seeking a court order against the content provider, member or user, the removed content may be replaced, or access to it restored, in 10 to 14 business days or more after receipt of the counter-notice, at S3mer\'s sole discretion.</p>\n\n		<p>10. Warranty Disclaimer</p>\n		<p>YOU AGREE THAT YOUR USE OF THE S3MER WEBSITE SHALL BE AT YOUR SOLE RISK. TO THE FULLEST EXTENT PERMITTED BY LAW, S3MER, ITS OFFICERS, DIRECTORS, EMPLOYEES, AND AGENTS DISCLAIM ALL WARRANTIES, EXPRESS OR IMPLIED, IN CONNECTION WITH THE WEBSITE AND YOUR USE THEREOF. S3MER MAKES NO WARRANTIES OR REPRESENTATIONS ABOUT THE ACCURACY OR COMPLETENESS OF THIS SITE\'S CONTENT OR THE CONTENT OF ANY SITES LINKED TO THIS SITE AND ASSUMES NO LIABILITY OR RESPONSIBILITY FOR ANY (I) ERRORS, MISTAKES, OR INACCURACIES OF CONTENT, (II) PERSONAL INJURY OR PROPERTY DAMAGE, OF ANY NATURE WHATSOEVER, RESULTING FROM YOUR ACCESS TO AND USE OF OUR WEBSITE, (III) ANY UNAUTHORIZED ACCESS TO OR USE OF OUR SECURE SERVERS AND/OR ANY AND ALL PERSONAL INFORMATION AND/OR FINANCIAL INFORMATION STORED THEREIN, (IV) ANY INTERRUPTION OR CESSATION OF TRANSMISSION TO OR FROM OUR WEBSITE, (IV) ANY BUGS, VIRUSES, TROJAN HORSES, OR THE LIKE WHICH MAY BE TRANSMITTED TO OR THROUGH OUR WEBSITE BY ANY THIRD PARTY, AND/OR (V) ANY ERRORS OR OMISSIONS IN ANY CONTENT OR FOR ANY LOSS OR DAMAGE OF ANY KIND INCURRED AS A RESULT OF THE USE OF ANY CONTENT POSTED, EMAILED, TRANSMITTED, OR OTHERWISE MADE AVAILABLE VIA THE S3MER WEBSITE. S3MER DOES NOT WARRANT, ENDORSE, GUARANTEE, OR ASSUME RESPONSIBILITY FOR ANY PRODUCT OR SERVICE ADVERTISED OR OFFERED BY A THIRD PARTY THROUGH THE S3MER WEBSITE OR ANY HYPERLINKED WEBSITE OR FEATURED IN ANY BANNER OR OTHER ADVERTISING, AND S3MER WILL NOT BE A PARTY TO OR IN ANY WAY BE RESPONSIBLE FOR MONITORING ANY TRANSACTION BETWEEN YOU AND THIRD-PARTY PROVIDERS OF PRODUCTS OR SERVICES. AS WITH THE PURCHASE OF A PRODUCT OR SERVICE THROUGH ANY MEDIUM OR IN ANY ENVIRONMENT, YOU SHOULD USE YOUR BEST JUDGMENT AND EXERCISE CAUTION WHERE APPROPRIATE.</p>\n\n		<p>11. Limitation of Liability</p>\n		<p>IN NO EVENT SHALL S3MER, ITS OFFICERS, DIRECTORS, EMPLOYEES, OR AGENTS, BE LIABLE TO YOU FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, PUNITIVE, OR CONSEQUENTIAL DAMAGES WHATSOEVER RESULTING FROM ANY (I) ERRORS, MISTAKES, OR INACCURACIES OF CONTENT, (II) PERSONAL INJURY OR PROPERTY DAMAGE, OF ANY NATURE WHATSOEVER, RESULTING FROM YOUR ACCESS TO AND USE OF OUR WEBSITE, (III) ANY UNAUTHORIZED ACCESS TO OR USE OF OUR SECURE SERVERS AND/OR ANY AND ALL PERSONAL INFORMATION AND/OR FINANCIAL INFORMATION STORED THEREIN, (IV) ANY INTERRUPTION OR CESSATION OF TRANSMISSION TO OR FROM OUR WEBSITE, (IV) ANY BUGS, VIRUSES, TROJAN HORSES, OR THE LIKE, WHICH MAY BE TRANSMITTED TO OR THROUGH OUR WEBSITE BY ANY THIRD PARTY, AND/OR (V) ANY ERRORS OR OMISSIONS IN ANY CONTENT OR FOR ANY LOSS OR DAMAGE OF ANY KIND INCURRED AS A RESULT OF YOUR USE OF ANY CONTENT POSTED, EMAILED, TRANSMITTED, OR OTHERWISE MADE AVAILABLE VIA THE S3MER WEBSITE, WHETHER BASED ON WARRANTY, CONTRACT, TORT, OR ANY OTHER LEGAL THEORY, AND WHETHER OR NOT THE COMPANY IS ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. THE FOREGOING LIMITATION OF LIABILITY SHALL APPLY TO THE FULLEST EXTENT PERMITTED BY LAW IN THE APPLICABLE JURISDICTION.</p>\n		<p>YOU SPECIFICALLY ACKNOWLEDGE THAT S3MER SHALL NOT BE LIABLE FOR USER SUBMISSIONS OR THE DEFAMATORY, OFFENSIVE, OR ILLEGAL CONDUCT OF ANY THIRD PARTY AND THAT THE RISK OF HARM OR DAMAGE FROM THE FOREGOING RESTS ENTIRELY WITH YOU.</p>\n\n		<p>12. Indemnity</p>\n		<p>You agree to defend, indemnify and hold harmless S3mer, its parent corporation, officers, directors, employees and agents, from and against any and all claims, damages, obligations, losses, liabilities, costs or debt, and expenses (including but not limited to attorney\'s fees) arising from: (i) your use of and access to the S3mer Website; (ii) your violation of any term of these Terms of Service; (iii) your violation of any third party right, including without limitation any copyright, property, or privacy right; or (iv) any claim that one of your User Submissions caused damage to a third party. This defense and indemnification obligation will survive these Terms of Service and your use of the S3mer Website.</p>\n\n		<p>13. Ability to Accept Terms of Service</p>\n		<p>You affirm that you are either more than 18 years of age, or an emancipated minor, or possess legal parental or guardian consent, and are fully able and competent to enter into the terms, conditions, obligations, affirmations, representations, and warranties set forth in these Terms of Service, and to abide by and comply with these Terms of Service. In any case, you affirm that you are over the age of 13, as the S3mer Website is not intended for children under 13. If you are under 13 years of age, then please do not use the S3mer Website.</p> \n\n		<p>14. Assignment</p>\n		<p>These Terms of Service, and any rights and licenses granted hereunder, may not be transferred or assigned by you, but may be assigned by S3mer without restriction.</p>'),(311,'es','PrivacyPolicy','<p><strong>s3mer, Inc: Privacy Notice</strong></p>\n		<p>As of May 1, 2008</p>\n		<p><strong>Your privacy is important to s3mer, inc. Therefore, s3mer is committed to respecting your privacy and the confidentiality of your personal data and messaging content.</strong></p>\n		<p><strong>The following describes privacy practices specific to s3mer. To understand how we treat the information you give us as you use s3mer, you should read this policy.</strong></p>\n\n		<p>1. Personal Information</p>\n		<p>1.1 Non-Account Activity. You can watch multimedia content on s3mer without having an s3mer Account. You can also contact us about a particular content without having an s3mer Account.</p>\n		<p>1.2 Account-Related Activity. Certain activities on s3mer - like uploading videos, posting messages - require you to have an s3mer Account. We ask for some personal information when you create an s3mer Account, including your email address and a password, which is used to protect your account from unauthorized access.</p>\n		<p>1.3 Usage Information. We may record information about your usage, such as when you use s3mer, the services, components, and external data sources you subscribe to, the type of multimedia content you upload, the multimedia you play, and the frequency and size of data transfers, as well as information you display or click on in s3mer (including UI elements, settings, and other information). If you are logged in, we may associate that information with your account. We may use clear GIFs (a.k.a. \"Web Beacons\") in HTML-based emails sent to our users to track which emails are opened by recipients.</p>\n		<p>1.4 Content Uploaded to Site. Any personal information or multimedia content that you voluntarily disclose online (on discussion boards, in messages and chat areas, or within your playback, etc.) becomes publicly available and can be collected and used by others.</p>\n\n		<p>2. Uses</p>\n		<p>2.1 If you submit personally identifiable information to us through the s3mer Site, we use your personal information to operate, maintain, and provide to you the general and personalized features and functionality of the s3mer Site, and to process any correspondence you send to us.</p>\n		<p>2.2 Any multimedia content that you submit to the s3mer Site may be redistributed through the internet and other media channels, and may be viewed by the general public.</p>\n		<p>2.3 We do not use your email address or other personally identifiable information to send commercial or marketing messages without your consent or except as part of a specific program or feature for which you will have the ability to opt-in or opt-out. We may, however, use your email address without further consent for non-marketing or administrative purposes (such as notifying you of major S3mer Site changes or for customer service purposes).</p>\n		<p>2.4 We use both your personally identifiable information and certain non-personally-identifiable information (such as anonymous user usage data, cookies, IP addresses, browser type, click stream data, etc.) to improve the quality and design of the S3mer Site and to create new features, promotions, functionality, and services by storing, tracking, analyzing, and processing user preferences and trends, as well as user activity and communications.</p>\n		<p>2.5 We use cookies, clear gifs, and log file information to: (a) store information so that you will not have to re-enter it during your visit or the next time you visit the S3mer Site; (b) provide custom, personalized content and information; (c) monitor the effectiveness of our marketing campaigns; (d) monitor aggregate metrics such as total number of visitors, pages viewed, etc.; and (e) track your entries, submissions, and status in promotions, sweepstakes, and contests. If you download the S3mer Player application, your copy of Player includes a unique application number. The unique application number and information about your installation of the Player (e.g. version number, language) will be sent to S3mer when the Player automatically checks for updates.</p>\n\n		<p>3. Your Choices</p>\n		<p>3.1 You may, of course, decline to submit personally identifiable information through the s3mer Site, in which case s3mer may not be able to provide certain services to you. Some advanced s3mer features may, for authentication purposes, require you to sign up. The privacy notices of those services govern the use of your personal information associated with them.</p>\n		<p>3.2 You may update or correct your personal profile information and email preferences at any time by visiting your settings page.</p>\n\n		<p>4. More information\n		Notice: We may change this Privacy Policy as a result of a change of policy in our company. If you have any questions regarding our Privacy Statement, or any requests to correct or clarify your personally identifying information, or requests to modify your \"opt-out\" preferences can be directed to:</p>\n\n		<p>s3mer, inc.</p>\n		<p>529 Andaluc&iacute;a Ave.</p>\n		<p>San Juan, PR 00920</p>\n		<p>E-mail: privacy.policy@s3mer.com</p>'),(312,'pt','PrivacyPolicy','<p><strong>s3mer, Inc: Privacy Notice</strong></p>\n		<p>As of May 1, 2008</p>\n		<p><strong>Your privacy is important to s3mer, inc. Therefore, s3mer is committed to respecting your privacy and the confidentiality of your personal data and messaging content.</strong></p>\n		<p><strong>The following describes privacy practices specific to s3mer. To understand how we treat the information you give us as you use s3mer, you should read this policy.</strong></p>\n\n		<p>1. Personal Information</p>\n		<p>1.1 Non-Account Activity. You can watch multimedia content on s3mer without having an s3mer Account. You can also contact us about a particular content without having an s3mer Account.</p>\n		<p>1.2 Account-Related Activity. Certain activities on s3mer - like uploading videos, posting messages - require you to have an s3mer Account. We ask for some personal information when you create an s3mer Account, including your email address and a password, which is used to protect your account from unauthorized access.</p>\n		<p>1.3 Usage Information. We may record information about your usage, such as when you use s3mer, the services, components, and external data sources you subscribe to, the type of multimedia content you upload, the multimedia you play, and the frequency and size of data transfers, as well as information you display or click on in s3mer (including UI elements, settings, and other information). If you are logged in, we may associate that information with your account. We may use clear GIFs (a.k.a. \"Web Beacons\") in HTML-based emails sent to our users to track which emails are opened by recipients.</p>\n		<p>1.4 Content Uploaded to Site. Any personal information or multimedia content that you voluntarily disclose online (on discussion boards, in messages and chat areas, or within your playback, etc.) becomes publicly available and can be collected and used by others.</p>\n\n		<p>2. Uses</p>\n		<p>2.1 If you submit personally identifiable information to us through the s3mer Site, we use your personal information to operate, maintain, and provide to you the general and personalized features and functionality of the s3mer Site, and to process any correspondence you send to us.</p>\n		<p>2.2 Any multimedia content that you submit to the s3mer Site may be redistributed through the internet and other media channels, and may be viewed by the general public.</p>\n		<p>2.3 We do not use your email address or other personally identifiable information to send commercial or marketing messages without your consent or except as part of a specific program or feature for which you will have the ability to opt-in or opt-out. We may, however, use your email address without further consent for non-marketing or administrative purposes (such as notifying you of major S3mer Site changes or for customer service purposes).</p>\n		<p>2.4 We use both your personally identifiable information and certain non-personally-identifiable information (such as anonymous user usage data, cookies, IP addresses, browser type, click stream data, etc.) to improve the quality and design of the S3mer Site and to create new features, promotions, functionality, and services by storing, tracking, analyzing, and processing user preferences and trends, as well as user activity and communications.</p>\n		<p>2.5 We use cookies, clear gifs, and log file information to: (a) store information so that you will not have to re-enter it during your visit or the next time you visit the S3mer Site; (b) provide custom, personalized content and information; (c) monitor the effectiveness of our marketing campaigns; (d) monitor aggregate metrics such as total number of visitors, pages viewed, etc.; and (e) track your entries, submissions, and status in promotions, sweepstakes, and contests. If you download the S3mer Player application, your copy of Player includes a unique application number. The unique application number and information about your installation of the Player (e.g. version number, language) will be sent to S3mer when the Player automatically checks for updates.</p>\n\n		<p>3. Your Choices</p>\n		<p>3.1 You may, of course, decline to submit personally identifiable information through the s3mer Site, in which case s3mer may not be able to provide certain services to you. Some advanced s3mer features may, for authentication purposes, require you to sign up. The privacy notices of those services govern the use of your personal information associated with them.</p>\n		<p>3.2 You may update or correct your personal profile information and email preferences at any time by visiting your settings page.</p>\n\n		<p>4. More information\n		Notice: We may change this Privacy Policy as a result of a change of policy in our company. If you have any questions regarding our Privacy Statement, or any requests to correct or clarify your personally identifying information, or requests to modify your \"opt-out\" preferences can be directed to:</p>\n\n		<p>s3mer, inc.</p>\n		<p>529 Andaluc&iacute;a Ave.</p>\n		<p>San Juan, PR 00920</p>\n		<p>E-mail: privacy.policy@s3mer.com</p>'),(313,'en','CopyrightInfo','<p>&nbsp;</p>\r\n<p>The s3mer name, the s3mer brand, the s3mer logo, the s3mer player and all the contents of this web site are property of s3mer inc. of San Juan, Puerto Rico.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Copyright 2008-2009. All rights reserved.</p>'),(314,'pt','CopyrightInfo','<p>&nbsp;</p>\r\n<p>The s3mer name, the s3mer brand, the s3mer logo, the s3mer player and all the contents of this web site are property of s3mer inc. of San Juan, Puerto Rico.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Copyright 2008-2009. All rights reserved.</p>'),(315,'es','CopyrightInfo','<p>&nbsp;</p>\r\n<p>The s3mer name, the s3mer brand, the s3mer logo, the s3mer player and all the contents of this web site are property of s3mer inc. of San Juan, Puerto Rico.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Copyright 2008-2009. All rights reserved.</p>'),(316,'pt','Create New Folder','Criar nova pasta'),(327,'es','OptionalDownload','<p>If you wish to download the application and install it yourself. First download the latest version of <a href=\"http://get.adobe.com/air/\">Adobe AIR here</a>, then download the <a href=\"http://media1.s3mer.com/app/S3mer_0991.air\">s3mer app .air package here</a> and install it.</p>\n<p>&nbsp;</p>'),(381,'pt','email_text','<span>Get your questions answered now!</span>\n<p></p>\nEmail <a href=\"mailto:support@s3mer.com\" style=\"color:#FFF\">support@s3mer.com</a>'),(326,'en','OptionalDownload','<p>If you wish to download the application and install it yourself. First download the latest version of <a href=\"http://get.adobe.com/air/\">Adobe AIR here</a>, then download the <a href=\"http://media1.s3mer.com/app/S3mer_0991.air\">s3mer app .air package here</a> and install it.</p>\n<p>&nbsp;</p>'),(379,'en','email_text','<span>Get your questions answered now!</span>\n<p></p>\nEmail <a href=\"mailto:support@s3mer.com\" style=\"color:#FFF\">support@s3mer.com</a>'),(380,'es','email_text','<span>Obtenga respuestas a sus preguntas ahora</span>\n<p></p>\nEmail <a href=\"mailto:support@s3mer.com\" style=\"color:#FFF\">support@s3mer.com</a>'),(317,'pt','Show Name','Nome do Espet&aacute;culo'),(318,'es','Show Description','Descripci&oacute;n del Espect&aacute;culo'),(319,'pt','Show Description','Descri&ccedil;&atilde;o do Espet&aacute;culo'),(320,'en','DemoVideo','See the <a href=\"#video_modal_contents\" id=\"demo_video_modal\">Demo Video</a>,'),(321,'es','DemoVideo','Vea el <a href=\"#video_modal_contents\" id=\"demo_video_modal\">V&iacute;deo de demostraci&oacute;n</a>,'),(322,'pt','DemoVideo','Veja o <a href=\"#video_modal_contents\" id=\"demo_video_modal\">v&iacute;deo de demostra&ccedil;&atilde;o</a>,'),(323,'en','IEissues','<img src=\"images/icons/error.png\" class=\"error-icon\"/>\nInternet Explorer Alert.\n</p>\n<p>\nIf you are using Internet Explorer 6 please upgrade to the newest version.\n</p>\n<p>\nWe support Internet Explorer 7 but prefer <a href=\"http://www.mozilla.com\">Firefox</a>'),(373,'es','s3mer Pro Players. When you press the checkout button you will be re-directed to Paypal where you will finish your transaction. When your transaction is completed you will be redirected to back to the','reproductor(es) pro. Cuando presione el bot&oacute;n de procesar transacci&oacute;n usted ser&aacute; dirigido a Paypal en donde completar&aacute; la transacci&oacute;n de pago. Cuando su transacci&oacute;n est&eacute; completa, usted ser&aacute; dirigido nuevamente al website de s3mer.'),(324,'es','IEissues','<img src=\"images/icons/error.png\" class=\"error-icon\"/>\nUsted esta usando Internet Explorer.\n</p>\n<p>\nEn estos momentos estamos experimentando ciertos problemas con Internet Explorer.\n</p>\n<p>\nRecomendamos que utilize <a href=\"http://www.mozilla.com\">Firefox</a> o <a href=\"http://www.apple.com/safari/download/\">Safari</a>'),(325,'pt','IEissues','<img src=\"images/icons/error.png\" class=\"error-icon\"/>\nYou are using Internet Explorer.\n</p>\n<p>\nCurrently we are experiencing some issues with Internet Explorer.\n</p>\n<p>\nWe recommend that you upgrade to <a href=\"http://www.mozilla.com\">Firefox</a> or <a href=\"http://www.apple.com/safari/download/\">Safari</a>'),(328,'pt','OptionalDownload','<p>If you wish to download the application and install it yourself. First download the latest version of <a href=\"http://get.adobe.com/air/\">Adobe AIR here</a>, then download the <a href=\"http://media1.s3mer.com/app/S3mer_0991.air\">s3mer app .air package here</a> and install it.</p>\n<p>&nbsp;</p>'),(376,'en','phone_text','<span>Have a question?</span>\n<p></p>\n<a href=\"mailto:support@s3mer.com\" style=\"color:#FFF\">Email us at support@s3mer.com</a>'),(377,'es','phone_text','<span>&iquest;Tiene Preguntas?</span>\n<p></p>\n<a href=\"mailto:support@s3mer.com\" style=\"color:#FFF\">Escriba a support@s3mer.com</a>'),(378,'pt','phone_text','<span>Have a question?</span>\n<p></p>\n<a href=\"mailto:support@s3mer.com\" style=\"color:#FFF\">Email us at support@s3mer.com</a>'),(329,'en','RegisterOrDemoBanner','<a href=\"register.php\">Register Free</a> or <a href=\"#video_modal_contents\" id=\"demo_video_modal\">See Demo Video</a>'),(330,'es','RegisterOrDemoBanner','<a href=\"register.php\">Crear Cuenta Gratis</a> o <a href=\"#video_modal_contents\" id=\"demo_video_modal\">Ver Video Demo</a>'),(331,'pt','RegisterOrDemoBanner','<a href=\"register.php\">Register Free</a> or <a href=\"#video_modal_contents\" id=\"demo_video_modal\">See Demo Video</a>'),(332,'en','WhatIsS3mer','What Is s3mer?'),(333,'en','DescWhatIsS3mer','s3mer is a complete dynamic <a href=\"http://en.wikipedia.org/wiki/Digital_signage\">digital signage</a> solution designed to be easy to use, cross platform and feature rich.'),(334,'en','Homepage2','<div class=\"hWrapper\">\n	<div class=\"cBanner homeBig\" style=\"height:57px;padding-top:10px;background-image: url(images/newhome/signupbanner.png);background-repeat: no-repeat;\">\n		<div style=\"text-align: center\"><a href=\"register.php\">Sign-up Free</a> or <a href=\"tour.php\">Take a Tour</a></div>\n	</div>\n	<div class=\"lColumn\">\n		<img src=\"images/monitor2.png\" width=\"300\" height=\"350\" alt=\"Monitor2\" /><br />\n	</div>\n	<div class=\"rColumn\">\n		<p><span class=\"homeH1\">What is s3mer?</span></p>\n		<p>s3mer is a complete dynamic <a href=\"http://en.wikipedia.org/wiki/Digital_signage\">digital signage</a> solution designed to be easy to use, cross platform and feature rich. <a href=\"tour.php\">More Info</a></p>\n		<br />\n        <iframe src=\"badge/index.html\" frameborder=\"0\" scrolling=\"no\" height=\"190\"></iframe>\n		<p><img src=\"images/newhome/otherlogos.png\" width=\"325\" height=\"81\" alt=\"Otherlogos\" /></p>\n	</div>\n	<!-- aligner --><div class=\"cBanner\"></div>\n	<div class=\"lColumn\">\n		<span class=\"homeH1\">The s3mer Demo</span>\n		<p>If you want to test s3mer you can <a href=\"register.php\">sign-up</a> for a free acount or you can <span class=\"hHilite\">install the s3mer Player Application and see a demo show.</span></p><br />\n		<p style=\"font-weight:bold\">To Install click on the Install Now Button or follow the instrucition on the manual installation procedure below.</p>\n	</div>\n	<div class=\"rColumn\"><br />\n        <p><span class=\"homeH2\">Some of the features of s3mer:</span></p>\n		<ul>\n			<li>Runs Mac OS X, Windows XP &amp; Vista</li>\n			<li>HD video playback</li>\n			<li>MPEG4 and FLV video format support</li>\n			<li>Flash SWF animation support</li>\n			<li><a href=\"http://en.wikipedia.org/wiki/Rss\">RSS</a> Support</li>\n			<li><a href=\"http://en.wikipedia.org/wiki/Video_podcast\">Video Podcast</a> Enabled</li>\n			<li>PNG, JPEG &amp; GIF image format support</li>\n		</ul>\n\n	</div>\n	<br /><br />\n	<div class=\"cBanner\" style=\"height:167px;padding-top:10px;background-image: url(images/newhome/manualbanner.png);background-repeat: no-repeat;\">\n		<span class=\"homeH1\" style=\"margin-left:15px;position:relative;top:-5px\">Manual Installation</span>\n	<div align=\"center\"><p style=\"width:96%;text-align:left\">The s3mer Player Application is based on the <a href=\"http://www.adobe.com/products/air/\">Adobe&reg; AIR&trade;</a> runtime. In order to install the player you need to first install the runtime from Adobe and then download and install the s3mer Player Application Package.</p></div><br />\n		<div class=\"lColumn\" style=\"text-align:center\">\n			<a href=\"http://get.adobe.com/air/otherversions/\">Download Adobe&reg; AIR&trade; Runtime</a>\n		</div>\n		<div class=\"rColumn\" style=\"text-align:center\">\n			<a href=\"http://media1.s3mer.com/app/S3mer_latest.air\">Download s3mer PlayerApp</a>\n		</div>\n	</div>\n	<div class=\"lColumn\">\n		<span class=\"homeH1\">Tutorial Video</span>\n		<p>Before you start taking advantage of s3mer we recommend that you watch our tutorial video. This video will help you setup your account and create your first show. Click on the video below to watch or <a href=\"tour.php\">Take a Tour</a>.</p><br />\n		<p>Press the&nbsp;&nbsp;<img src=\"images/newhome/fullscreen.png\" alt=\"Fullscreen\"/>&nbsp;&nbsp;icon to view in full screen</p>\n		<embed src=\"http://blip.tv/play/41643iIA\" type=\"application/x-shockwave-flash\" width=\"313\" height=\"209\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>\n	</div>\n	<div class=\"rColumn\">\n		<span class=\"homeH1\">Tips, Tricks &amp; Updates</span>\n		<p>To stay up to date on new features, tips and tricks related to s3mer please visit <a href=\"http://s3mer.tumblr.com/\">our blog</a>.</p><br />\n		<p style=\"font-weight:bold\"><a href=\"http://s3mer.tumblr.com/\">Click here for The s3mer blog</a></p><br />\n		<span class=\"homeH1\">Newsletter</span>\n		<p>Please leave your name and email if you wish to recieve our newsletter.</p>\n		<div class=\"hNewsletter\">\n			<form action=\"http://s3merinc.cmail1.com/s/337333/\" method=\"post\">\n			<div>\n			<label for=\"name\">Name:</label><br /><input type=\"text\" name=\"name\" id=\"name\" /><br />\n			<label for=\"l337333-337333\">Email:</label><br /><input type=\"text\" name=\"cm-337333-337333\" id=\"l337333-337333\" /><br />\n			<div class=\"buttons\">\n				<button type=\"submit\">\n					<img alt=\"\" src=\"images/icons/email_add.png\"/> \n					Subscribe\n				</button>\n			</div>\n			</div>\n			</form>\n		</div>\n	</div>\n	<div class=\"cBanner\" style=\"height:130px;padding-top:10px;background-image: url(images/newhome/feedbackbanner.png);background-repeat: no-repeat;margin-top:20px\">\n		<span class=\"homeH1\" style=\"margin-left:15px;position:relative;top:-5px\">Feedback</span>\n		<div align=\"center\"><p style=\"width:96%;text-align:left\">We want to know what you think about our product. Please write your commets to <a href=\"mailto:feedback@s3mer.com\">feedback@s3mer.com</a>. If you expirience and issue using s3mer or if you want to report a bug you can check out our <a href=\"http://groups.google.com/group/s3mer\">discussion group</a>, you can chat with one of our support technicians or you can write an email to <a href=\"mailto:support@s3mer.com\">support@s3mer.com</a></p></div>\n	</div>\n</div>'),(369,'es','More Information','M&aacute;s Informaci&oacute;n'),(370,'es','Checkout','Efectuar Transacci&oacute;n'),(371,'es','Order Details','Detalles de Orden'),(372,'es','You are about to register','Usted est&aacute; pr&oacute;ximo a registrar'),(335,'es','Homepage2','<div class=\"hWrapper\">\n	<div class=\"cBanner homeBig\" style=\"height:57px;padding-top:10px;background-image: url(images/newhome/signupbanner.png);background-repeat: no-repeat;\">\n		<div style=\"text-align: center\"><a href=\"register.php\">Crear Cuenta Gratis</a> o <a href=\"tour.php\">Tomar un Recorrido</a></div>\n	</div>\n	<div class=\"lColumn\">\n		<img src=\"images/monitor2.png\" width=\"300\" height=\"350\" alt=\"Monitor2\" /><br />\n	</div>\n	<div class=\"rColumn\">\n		<p><span class=\"homeH1\">Qu&eacute; es s3mer?</span></p>\n		<p>s3mer es una soluci&oacute;n completa para crear r&oacute;tulos digitales (\"<a href=\"http://en.wikipedia.org/wiki/Digital_signage\">digital signage</a>\"), f&aacute;cil de usar y que funciona en m&uacute;ltiples plataformas.</p>\n		<br />\n		<p><span class=\"homeH2\">Algunas de las capacidades de s3mer:</span></p>\n		<ul>\n			<li>Funciona en Mac OS X, Windows XP, Vista &amp; Linux</li>\n			<li>Reproducci&oacute;n de Video HD</li>\n			<li>Soporte para formatos 	MPEG4 y FLV</li>\n			<li>Soporte para Animaci&oacute;n Flash SWF</li>\n			<li>Subscripciones a <a href=\"http://es.wikipedia.org/wiki/Rss\">RSS</a></li>\n			<li>Subscripciones a <a href=\"http://es.wikipedia.org/wiki/Podcast\">Podcasts de Video</a></li>\n			<li>Formato de im&aacute;genes PNG, JPEG &amp; GIF</li>\n		</ul>\n		<p><img src=\"images/newhome/otherlogos.png\" width=\"325\" height=\"81\" alt=\"Otherlogos\" /></p>\n	</div>\n	<!-- aligner --><div class=\"cBanner\"></div>\n	<div class=\"lColumn\">\n		<span class=\"homeH1\">El Demo s3mer</span>\n		<p>Si quiere probar s3mer puede <a href=\"register.php\">registrarse</a> para crear una cuenta gratuita o puede <span class=\"hHilite\">instalar la aplicaci&oacute;n s3mer Player y mirar un espect&aacute;culo de demostraci&oacute;n.</span></p><br />\n		<p style=\"font-weight:bold\">Para instalar oprima el bot&oacute;n \"Install Now\" o siga las instrucciones de instalaci&oacute;n manual abajo.</p>\n	</div>\n	<div class=\"rColumn\" align=\"right\" style=\"height:224px\"><br />\n\n				<iframe src=\"badge/index.html\" frameborder=\"0\" scrolling=\"no\" height=\"190\"></iframe>\n\n	</div>\n	<div class=\"cBanner\" style=\"height:167px;padding-top:10px;background-image: url(images/newhome/manualbanner.png);background-repeat: no-repeat;\">\n		<span class=\"homeH1\" style=\"margin-left:15px;position:relative;top:-5px\">Instalaci&oacute;n Manual</span>\n	<div align=\"center\"><p style=\"width:96%;text-align:left\">La aplicaci&oacute;n s3mer Player esta basada en tecnolog&iacute;a <a href=\"http://www.adobe.com/products/air/\">Adobe&reg; AIR&trade;</a>. para instalar el s3mer Player debe primero descargar e instalar el Adobe AIR, luego descargue e instale el s3mer PlayerApp.</p></div><br />\n		<div class=\"lColumn\" style=\"text-align:center\">\n			<a href=\"http://get.adobe.com/air/otherversions/\">Descargar Adobe&reg; AIR&trade; Runtime</a>\n		</div>\n		<div class=\"rColumn\" style=\"text-align:center\">\n			<a href=\"http://media1.s3mer.com/app/S3mer_latest.air\">Descargar s3mer PlayerApp</a>\n		</div>\n	</div>\n	<div class=\"lColumn\">\n		<span class=\"homeH1\">Video Entrenamiento</span>\n		<p>Recomendamos a todos nuestro usuarios que vean este video introductor que le ayudar&aacute; a crear su primer espect&aacute;culo. Haga click en el video para ver o <a href=\"tour.php\">Tome un Recorrido</a>.</p><br />\n		<p>Presione el icono &nbsp;<img src=\"images/newhome/fullscreen.png\" alt=\"Fullscreen\"/>&nbsp;&nbsp;para ver en pantalla completa.</p>\n		<embed src=\"http://blip.tv/play/41643iIA\" type=\"application/x-shockwave-flash\" width=\"313\" height=\"209\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>\n	</div>\n	<div class=\"rColumn\">\n		<span class=\"homeH1\">Consejos &amp; Actualizaciones</span>\n		<p>Para mantenerte al corriente en cuanto a consejos o actualizaciones de s3mer visita <a href=\"http://s3mer.tumblr.com/\">nuestro blog</a>.</p><br />\n		<p style=\"font-weight:bold\"><a href=\"http://s3mer.tumblr.com/\">Haz click aqu&iacute; para \"The s3mer blog\"</a></p><br />\n		<span class=\"homeH1\">Bolet&iacute;n</span>\n		<p>Por favor deja tu nombre y correo electr&oacute;nico para que recibas nuestro bolet&iacute;n de noticias.</p>\n		<div class=\"hNewsletter\">\n			<form action=\"http://s3merinc.cmail1.com/s/337333/\" method=\"post\">\n			<div>\n			<label for=\"name\">Nombre:</label><br /><input type=\"text\" name=\"name\" id=\"name\" /><br />\n			<label for=\"l337333-337333\">Correo:</label><br /><input type=\"text\" name=\"cm-337333-337333\" id=\"l337333-337333\" /><br />\n			<div class=\"buttons\">\n				<button type=\"submit\">\n					<img alt=\"\" src=\"images/icons/email_add.png\"/> \n					Subscribir\n				</button>\n			</div>\n			</div>\n			</form>\n		</div>\n	</div>\n	<div class=\"cBanner\" style=\"height:130px;padding-top:10px;background-image: url(images/newhome/feedbackbanner.png);background-repeat: no-repeat;margin-top:20px\">\n		<span class=\"homeH1\" style=\"margin-left:15px;position:relative;top:-5px\">Opini&oacute;n</span>\n		<div align=\"center\"><p style=\"width:96%;text-align:left\">Queremos saber lo que piensa sobre nuestro producto. Env&iacute;e sus comentarios a <a href=\"mailto:feedback@s3mer.com\">feedback@s3mer.com</a>. Si tiene alg&uacute;n problema con su cuenta o con el s3mer Player escriba en nuestro <a href=\"http://groups.google.com/group/s3mer\">grupo de discusi&oacute;n</a>, haga un \"chat\" en la parte superior del sitio o escriba un correo a <a href=\"mailto:support@s3mer.com\">support@s3mer.com</a>.</p></div>\n	</div>\n</div>'),(364,'es','Details','Detalles'),(365,'es','s3mer Enterprise System','Sistema s3mer Empresas'),(366,'es','s3mer Pro is our best digital signage software available at any price. With the Pro version you get','s3mer pro es el mejor software de digital signage disponible en cualquier precio. Con nuestra versi&oacute;n usted obtendr&aacute;'),(367,'es','We quote on a case by case basis','Cotizamos cada caso por separado, comuniquese con nosotros para mayor informaci&oacute;n'),(368,'es','s3mer Enterprise is the perfect solution for large deployments. You get all the features of s3mer Pro. With s3mer Enterprise you control your own application and media servers.','s3mer Empresas es la soluci&oacute;n ideal para sistemas de gran escala. Usted obtendr&aacute; todas las funciones de nuestro sistema pro. Con nuestro sistema empresas usted tendr&aacute; el control total y absoluto de su archivos y sus servidores.'),(336,'pt','Homepage2','<div class=\"hWrapper\">\n	<div class=\"cBanner homeBig\" style=\"height:57px;padding-top:10px;background-image: url(images/newhome/signupbanner.png);background-repeat: no-repeat;\">\n		<div style=\"text-align: center\"><a href=\"register.php\">Cadastre-se Gr&aacute;tis</a> ou <a href=\"tour.php\">Veja nossa apresenta&ccedil;&atilde;o</a></div>\n	</div>\n	<div class=\"lColumn\">\n		<img src=\"images/monitor2.png\" width=\"300\" height=\"350\" alt=\"Monitor2\" /><br />\n	</div>\n	<div class=\"rColumn\">\n		<p><span class=\"homeH1\">O que &eacute; s3mer?</span></p>\n		<p>s3mer &eacute; uma solu&ccedil;&atilde;o completa para gerenciamento de telas eletr&ocirc;nicas melhor conhecida como <a href=\"http://en.wikipedia.org/wiki/Digital_signage\">\"digital signage\"</a> projetada para ser facil sem sacrificar funcionalidade.</p>\n		<br />\n		<p><span class=\"homeH2\">Algumas caracter&iacute;sticas do s3mer s&atilde;o:</span></p>\n		<ul>\n			<li>Roda no Mac OS X, Windows XP, Vista &amp; Linux</li>\n			<li>Apoio para v&iacute;deo HD</li>\n			<li>Apoio para v&iacute;deo no formato MPEG4 e FLV</li>\n			<li>Apoio para anima&ccedil;&otilde;es Flash FLV</li>\n			<li>Apoio para <a href=\"http://en.wikipedia.org/wiki/Rss\">RSS</a></li>\n			<li>Apoio para <a href=\"http://en.wikipedia.org/wiki/Video_podcast\">Podcasts de V&iacute;deo</a></li>\n			<li>Apoio para formato de imagens JPG, PNG e GIF</li>\n		</ul>\n		<p><img src=\"images/newhome/otherlogos.png\" width=\"325\" height=\"81\" alt=\"Otherlogos\" /></p>\n	</div>\n	<!-- aligner --><div class=\"cBanner\"></div>\n	<div class=\"lColumn\">\n		<span class=\"homeH1\">O demo do s3mer</span>\n		<p>Se voc&ecirc; quiser testar o nosso produto, pode se  <a href=\"register.php\">cadastrar aqui</a> para criar uma conta gratuita ou pode <span class=\"hHilite\">instalar a aplica&ccedil;&atilde;o reprodutora do s3mer e ver o nosso espet&aacute;culo demo.</span></p><br />\n		<p style=\"font-weight:bold\">Para instalar clique no bot&atilde;o que diz Instale Agora ou siga as instru&ccedil;&otilde;es fornecidas na guia do usu&aacute;rio.</p>\n	</div>\n	<div class=\"rColumn\" align=\"right\" style=\"height:224px\"><br />\n\n				<iframe src=\"badge/index.html\" frameborder=\"0\" scrolling=\"no\" height=\"190\"></iframe>\n\n	</div>\n	<div class=\"cBanner\" style=\"height:167px;padding-top:10px;background-image: url(images/newhome/manualbanner.png);background-repeat: no-repeat;\">\n		<span class=\"homeH1\" style=\"margin-left:15px;position:relative;top:-5px\">Instala&ccedil;&atilde;o Manual</span>\n	<div align=\"center\"><p style=\"width:96%;text-align:left\">A aplica&ccedil;&atilde;o do s3mer foi desenvolvida utilizando <a href=\"http://www.adobe.com/products/air/\">Adobe&reg; AIR&trade;</a> e precisa seu int&eacute;rprete. Ap&oacute;s instalar o int&eacute;rprete para Adobe Air, instale a aplica&ccedil;&atilde;o do s3mer.</p></div><br />\n		<div class=\"lColumn\" style=\"text-align:center\">\n			<a href=\"http://get.adobe.com/air/otherversions/\">Descarregue o int&eacute;rprete para Adobe&reg; AIR&trade;</a>\n		</div>\n		<div class=\"rColumn\" style=\"text-align:center\">\n			<a href=\"http://media1.s3mer.com/app/S3mer_latest.air\">Descarregue o reprodutor do s3mer</a>\n		</div>\n	</div>\n	<div class=\"lColumn\">\n		<span class=\"homeH1\">V&iacute;deo Introductivo</span>\n		<p>N&oacute;s recomendamos que voc&ecirc; veja o v&iacute;deo introductivo antes de utilizar o s3mer. Este v&iacute;deo ajud&aacute;-lo-&aacute; criar sua conta e criar seu primeiro espet&aacute;culo. Clique no v&iacute;deo aqui abaixo ou <a href=\"tour.php\">Veja nossa apresenta&ccedil;&atilde;o</a>.</p><br />\n		<p>Clique o &iacute;cone&nbsp;&nbsp;<img src=\"images/newhome/fullscreen.png\" alt=\"Fullscreen\"/>&nbsp;&nbsp;para ver a tela cheia</p>\n		<embed src=\"http://blip.tv/play/41643iIA\" type=\"application/x-shockwave-flash\" width=\"313\" height=\"209\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>\n	</div>\n	<div class=\"rColumn\">\n		<span class=\"homeH1\">Dicas, truques &amp; atualiza&ccedil;&otilde;es</span>\n		<p>Para permanecer atualizado nas dicas e truques acerca do s3mer por favor visite <a href=\"http://s3mer.tumblr.com/\">o nosso blog</a>.</p><br />\n		<p style=\"font-weight:bold\"><a href=\"http://s3mer.tumblr.com/\">Clique aqui para ir ao blog do s3mer</a></p><br />\n		<span class=\"homeH1\">Not&iacute;cias</span>\n		<p>Por favor, deixe seu nome e correio eletr&ocirc;nico se voc&ecirc; quer receber nosso listado de not&iacute;cias.</p>\n		<div class=\"hNewsletter\">\n			<form action=\"http://s3merinc.cmail1.com/s/337333/\" method=\"post\">\n			<div>\n			<label for=\"name\">Nome:</label><br /><input type=\"text\" name=\"name\" id=\"name\" /><br />\n			<label for=\"l337333-337333\">Email:</label><br /><input type=\"text\" name=\"cm-337333-337333\" id=\"l337333-337333\" /><br />\n			<div class=\"buttons\">\n				<button type=\"submit\">\n					<img alt=\"\" src=\"images/icons/email_add.png\"/> \n					Cadastrar\n				</button>\n			</div>\n			</div>\n			</form>\n		</div>\n	</div>\n	<div class=\"cBanner\" style=\"height:130px;padding-top:10px;background-image: url(images/newhome/feedbackbanner.png);background-repeat: no-repeat;margin-top:20px\">\n		<span class=\"homeH1\" style=\"margin-left:15px;position:relative;top:-5px\">Sugest&otilde;es</span>\n		<div align=\"center\"><p style=\"width:96%;text-align:left\">N&oacute;s queremos saber o que voc&ecirc; acha acerca do nosso produto. Por favor, envie suas sugest&otilde;es a <a href=\"mailto:feedback@s3mer.com\">feedback@s3mer.com</a>. Se voc&ecirc; encontra-se com algum problema ao utilizar s3mer visite o nosso <a href=\"http://groups.google.com/group/s3mer\">grupo de discuss&atilde;o</a>, voc&ecirc; pode um dos nossos t&eacute;cnicos ou pode escrever a <a href=\"mailto:support@s3mer.com\">support@s3mer.com</a></p></div>\n	</div>\n</div>'),(356,'es','Price','Precio'),(357,'es','s3mer Pro Player','Reproductor s3mer pro'),(358,'es','5Gb of media storage per Pro Player','5Gb de espacio para media por cada reproductor pro'),(359,'es','Live Video Input','Capacidad de obtener video en vivo por medio de camaras y otras fuentes de video'),(360,'es','HTML Display','Capacidad de accesar un servidor web y mostrar HTML'),(361,'es','Show Customizations','Customizaci&oacute;n de espect&aacute;culos'),(362,'es','No Ads','Operaci&oacute;n libre de anuncios'),(363,'es','Upcoming Features','Acceso a nuevas funciones que est&aacute;n siendo desarrolladas.'),(337,'en','Tour','<div class=\"hWrapper\">\n	<br />\n	<div class=\"lColumn\">\n		<p><span class=\"homeH1\">What is s3mer?</span></p>\n		<p>s3mer is a piece of software that helps people and businesses create and maintain digital signs using flat screen TVs, projectors or even giant digital billboards as a display surface. Digital signs can display still images, animations, video and even live data from the web like weather, stocks, sports and even twitter activity.\n        </p>\n		<br />\n		<p><span class=\"homeH2\">Use s3mer for:</span></p>\n		<ul>\n			<li>In-Store Advertising</li>\n			<li>Professional Offices</li>\n			<li>Civic Buildings</li>\n			<li>Restaurants</li>\n			<li>Hospitality</li>\n			<li>Tourism</li>\n			<li>Entertainment</li>\n			<li>Healthcare</li>\n			<li>Events</li>\n		</ul>\n	</div>\n	<div class=\"rColumn\">\n		<img src=\"images/tour/tour1.png\" width=\"300\" height=\"350\" alt=\"Tour1\" /><br />\n	</div>\n	<div class=\"cBanner homeBig\" style=\"height:57px;padding-top:10px;background-image: url(images/newhome/signupbanner.png);background-repeat: no-repeat;\">\n		<div style=\"text-align: center\">After the tour remember to <a href=\"register.php\">Register Free</a></div>\n	</div><p>&nbsp;</p>\n	<div class=\"lColumn\">\n		<img src=\"images/tour/uploadmedia.png\" width=\"300\" height=\"105\" alt=\"Upload Media\" />\n		<span class=\"homeH1\">1. Upload Media Files</span>\n		<p>With s3mer is very easy to upload one or multiple files. Just go to the library click on the Upload button and select files from your hard drive.</p>\n		<p><span class=\"homeH2\">File Formats</span></p>\n		<ul>\n			<li>.mov - MPEG 4 Quicktime Movies</li>\n			<li>.mp4 - MPEG 4 Video</li>\n			<li>.m4v - MPEG 4 h.264</li>\n			<li>.flv - Flash Video</li>\n			<li>.swf - Flash Animations</li>\n			<li>.png - Portable Network Graphics</li>\n			<li>.jpg - JPEG Image Files</li>\n			<li>.gif - Graphics Interchange Format</li>\n		</ul>\n		<p><a href=\"#uploadmedia\" id=\"tour1_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Click Here to Watch Video</a></p>\n	</div>\n	<div class=\"rColumn\">\n		<img src=\"images/tour/newshow.png\" width=\"300\" height=\"105\" alt=\"New Show\" />\n		<span class=\"homeH1\">2. Create a Show</span>\n		<p>When you create show you define how the screen will look. With most other digital signage solutions this is a painful task but with s3mer is a breeze.</p>\n		<p><span class=\"homeH2\">Kinds of Media</span></p>\n		<ul>\n			<li>Video Files</li>\n			<li>Image Files</li>\n			<li>Flash Animations</li>\n			<li>RSS Feeds</li>\n			<li>Video Podcast Feeds</li>\n			<li>Live Video (coming soon)</li>\n			<li>HTML Pages (coming soon)</li>\n		</ul>\n		<p><a href=\"#createshow\" id=\"tour2_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Click Here to Watch Video</a></p>\n	</div>\n	<!-- aligner --><div class=\"cBanner\" style=\"margin-bottom:30px\"></div>\n	<div class=\"lColumn\">\n		<img src=\"images/tour/newplayer.png\" width=\"300\" height=\"105\" alt=\"New Player\" />\n		<span class=\"homeH1\">3. Create a Player</span>\n		<p>A player represents the physical computer that plays your shows. s3mer lets you schedule when a certain show is played by the player.</p>\n		<p><span class=\"homeH2\">Steps to create a player</span></p>\n		<ul>\n			<li>Click on new player button</li>\n			<li>Assign name and description</li>\n			<li>Add a show</li>\n			<li>Create a schedule</li>\n		</ul>\n		<p><a href=\"#createplayer\" id=\"tour3_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Click Here to Watch Video</a></p>\n		<p>&nbsp;</p>\n	</div>\n	<div class=\"rColumn\">\n		<img src=\"images/tour/installdemoplayer.png\" class=\"tourimage\" width=\"300\" height=\"105\" alt=\"Install Demo Player\" />\n		<span class=\"homeH1\">Run the Demo</span>\n		<p>If you like you can install the s3mer Player Application and run a demo show which displays most of the features of s3mer.</p>\n		<p><span class=\"homeH2\">Steps to Install the Player Application</span></p>\n		<ul>\n			<li>Go to <a href=\"index.php\">http://www.s3mer.com</a></li>\n			<li>Click on the install now button</li>\n			<li>Click Open</li>\n			<li>Click Install</li>\n			<li>Click Start Demo</li>\n		</ul>\n		<p><a href=\"#installdemoplayer\" id=\"tour4_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Click Here to Watch Video</a></p>\n	</div>\n</div>'),(338,'es','Tour','		<div class=\"hWrapper\">\n			<br />\n			<div class=\"lColumn\">\n				<p><span class=\"homeH1\">&iexcl;Es muy f&aacute;cil!</span></p>\n				<p>Con solo unos clicks podr&aacute;s crear y desplegar tu propia red de publicidad digital</p>\n				<br />\n				<p><span class=\"homeH2\">Puedes usar s3mer para:</span></p>\n				<ul>\n					<li>Publicidad dentro de tu Tienda</li>\n					<li>Oficinas Profesionales</li>\n					<li>Edificios P&uacute;blicos</li>\n					<li>Restaurantes</li>\n					<li>Hoteles</li>\n					<li>Turismo</li>\n					<li>Entretenimiento</li>\n					<li>Hospitales y Oficinas M&eacute;dicas</li>\n					<li>Eventos especiales</li>\n				</ul>\n			</div>\n			<div class=\"rColumn\">\n				<img src=\"images/tour/tour1.png\" width=\"300\" height=\"350\" alt=\"Tour1\" /><br />\n			</div>\n			<div class=\"cBanner homeBig\" style=\"height:57px;padding-top:10px;background-image: url(images/newhome/signupbanner.png);background-repeat: no-repeat;\">\n				<div style=\"text-align: center\">Despu&eacute;s del recorrido <a href=\"register.php\">Registrarte Gratis</a></div>\n			</div><p>&nbsp;</p>\n			<div class=\"lColumn\">\n				<img src=\"images/tour/uploadmedia.png\" width=\"300\" height=\"105\" alt=\"Upload Media\" />\n				<span class=\"homeH1\">1. Subir Contenidos</span>\n				<p>Con s3mer es muy f&aacute;cil subir uno o mas archivos a la vez. Oprimes el bot&oacute;n de subir y seleccionas los archivos que quieres a&ntilde;adir de tu disco duro.</p>\n				<p><span class=\"homeH2\">Formatos Aceptados</span></p>\n				<ul>\n					<li>.mov - V&iacute;deo MPEG 4 Quicktime</li>\n					<li>.mp4 - V&iacute;deo MPEG 4</li>\n					<li>.m4v - MPEG 4 h.264</li>\n					<li>.flv - V&iacute;deo Flash</li>\n					<li>.swf - Animaciones Flash</li>\n					<li>.png - Portable Network Graphics</li>\n					<li>.jpg - Archivos JPEG</li>\n					<li>.gif - Graphics Interchange Format</li>\n				</ul>\n				<p><a href=\"#uploadmedia\" id=\"tour1_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Presione aqu&iacute; para ver V&iacute;deo</a></p>\n			</div>\n			<div class=\"rColumn\">\n				<img src=\"images/tour/newshow.png\" width=\"300\" height=\"105\" alt=\"New Show\" />\n				<span class=\"homeH1\">2. Crear Espect&aacute;culo</span>\n				<p>Al crear un espect&aacute;culo defines como se ver&aacute; el producto final, la pantalla. A diferencia de otras soluciones de publicidad digital con s3mer esta tarea es muy f&aacute;cil.</p>\n				<p><span class=\"homeH2\">Tipos de Contenidos</span></p>\n				<ul>\n					<li>Archivos de V&iacute;deo</li>\n					<li>Archivos de Imagen</li>\n					<li>Animaciones Flash</li>\n					<li>RSS Feeds</li>\n					<li>Podcast de V&iacute;deo</li>\n					<li>V&iacute;deo en Directo (pr&oacute;ximamente)</li>\n					<li>P&aacute;ginas HTML (pr&oacute;ximamente)</li>\n				</ul>\n				<p><a href=\"#createshow\" id=\"tour2_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Presione aqu&iacute; para ver V&iacute;deo</a></p>\n			</div>\n			<!-- aligner --><div class=\"cBanner\" style=\"margin-bottom:30px\"></div>\n			<div class=\"lColumn\">\n				<img src=\"images/tour/newplayer.png\" width=\"300\" height=\"105\" alt=\"New Player\" />\n				<span class=\"homeH1\">3. Crear Reproductor</span>\n				<p>Un reproductor representa al computador f&iacute;sico que proyecta tus espect&aacute;culos. s3mer te permite asignar horarios para tus espect&aacute;culos por cada reproductor.</p>\n				<p><span class=\"homeH2\">Pasos para crear un reproductor</span></p>\n				<ul>\n					<li>Oprima el bot&oacute;n de nuevo reproductor</li>\n					<li>Asigne nombre y descripci&oacute;n</li>\n					<li>A&ntilde;ada un espect&aacute;culo</li>\n					<li>Si es necesario, designe un horario para cada espect&aacute;culo</li>\n				</ul>\n				<p><a href=\"#createplayer\" id=\"tour3_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Presione aqu&iacute; para ver V&iacute;deo</a></p>\n				<p>&nbsp;</p>\n			</div>\n			<div class=\"rColumn\">\n				<img src=\"images/tour/installdemoplayer.png\" class=\"tourimage\" width=\"300\" height=\"105\" alt=\"Install Demo Player\" />\n				<span class=\"homeH1\">Proyecte el Demo</span>\n				<p>Si lo desea puede instalar la aplicaci&oacute;n de s3mer Player y proyectar un espect&aacute;culo de demostraci&oacute;n donde prodr&aacute; ver las diferentes caracter&iacute;sticas de s3mer.</p>\n				<p><span class=\"homeH2\">Steps to Install the Player Application</span></p>\n				<ul>\n					<li>Vaya a <a href=\"index.php\">http://www.s3mer.com</a></li>\n					<li>Presione en el botton de install now</li>\n					<li>Presione Open</li>\n					<li>Presione Install</li>\n					<li>Presione Start Demo</li>\n				</ul>\n				<p><a href=\"#installdemoplayer\" id=\"tour4_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Presione aqu&iacute; para ver V&iacute;deo</a></p>\n			</div>\n		</div>                                                                                        '),(339,'pt','Tour','		<div class=\"hWrapper\">\n			<br />\n			<div class=\"lColumn\">\n				<p><span class=\"homeH1\">&Eacute; t&atilde;o f&aacute;cil!</span></p>\n				<p>Voc&ecirc; pode criar sua pr&oacute;pria rede de telas eletr&ocirc;nicas em apenas alguns passos</p>\n				<br />\n				<p><span class=\"homeH2\">Utilize o s3mer para:</span></p>\n				<ul>\n					<li>mercadejo na mesma loja</li>\n					<li>Escrit&oacute;rios profissionais</li>\n					<li>Edif&iacute;cios p&uacute;blicos</li>\n					<li>Restaurantes</li>\n					<li>Hot&eacute;is</li>\n					<li>Turismo</li>\n					<li>Entretenimento</li>\n					<li>Hospitais</li>\n					<li>Eventos</li>\n				</ul>\n			</div>\n			<div class=\"rColumn\">\n				<img src=\"images/tour/tour1.png\" width=\"300\" height=\"350\" alt=\"Tour1\" /><br />\n			</div>\n			<div class=\"cBanner homeBig\" style=\"height:57px;padding-top:10px;background-image: url(images/newhome/signupbanner.png);background-repeat: no-repeat;\">\n				<div style=\"text-align: center\">Lembre-se de <a href=\"register.php\">Cadastrar-se Gr&aacute;tis</a></div>\n			</div><p>&nbsp;</p>\n			<div class=\"lColumn\">\n				<img src=\"images/tour/uploadmedia.png\" width=\"300\" height=\"105\" alt=\"Upload Media\" />\n				<span class=\"homeH1\">1. Ponha os arquivos no servidor</span>\n				<p>s3mer faz muito f&aacute;cil por um ou m&uacute;ltiples arquivos no servidor de media. S&oacute; clique na parte da livraria e clique o bot&atilde;o de Upload e selecione os arquivos que deseja subir ao servidor.</p>\n				<p><span class=\"homeH2\">Formatos de Arquivos</span></p>\n				<ul>\n					<li>Filmes .mov - MPEG 4 Quicktime</li>\n					<li>V&iacute;deos .mp4 - MPEG 4</li>\n					<li>.m4v - MPEG 4 h.264</li>\n					<li>.flv - V&iacute;deos Flash</li>\n					<li>.swf - Anima&ccedil;&otilde;es Flash</li>\n					<li>.png - Portable Network Graphics</li>\n					<li>.jpg - Arquivos JPEG</li>\n					<li>.gif - Arquivos GIF</li>\n				</ul>\n				<p><a href=\"#uploadmedia\" id=\"tour1_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Clique aqui para ver o v&iacute;deo</a></p>\n			</div>\n			<div class=\"rColumn\">\n				<img src=\"images/tour/newshow.png\" width=\"300\" height=\"105\" alt=\"New Show\" />\n				<span class=\"homeH1\">2. Crie seu espet&aacute;culo</span>\n				<p>Quando voc&ecirc; cria seu espet&aacute;culo voc&ecirc; define como sua tela vai aparecer. Outras solu&ccedil;&otilde;es fazem disto um dor de cabe&ccedil;a mas com o s3mer fica muito f&aacute;cil.</p>\n				<p><span class=\"homeH2\">Tipos de Media</span></p>\n				<ul>\n					<li>Arquivos de V&iacute;deos</li>\n					<li>Arquivos de Imagens</li>\n					<li>Anima&ccedil;&otilde;es</li>\n					<li>RSS Feeds</li>\n					<li>Podcasts de V&iacute;deos</li>\n					<li>V&iacute;deo ao vivo</li>\n					<li>P&aacute;ginas HTML</li>\n				</ul>\n				<p><a href=\"#createshow\" id=\"tour2_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Clique aqui para ver o v&iacute;deo</a></p>\n			</div>\n			<!-- aligner --><div class=\"cBanner\" style=\"margin-bottom:30px\"></div>\n			<div class=\"lColumn\">\n				<img src=\"images/tour/newplayer.png\" width=\"300\" height=\"105\" alt=\"New Player\" />\n				<span class=\"homeH1\">3. Crie um reprodutor</span>\n				<p>Um reprodutor representa um micro f&iacute;sico que toca seus espet&aacute;culos. s3mer deixa nas suas manos quando os espet&aacute;culos s&atilde;o tocados.</p>\n				<p><span class=\"homeH2\">Passos para criar um reprodutor</span></p>\n				<ul>\n					<li>Clique o bot&atilde;o para criar um novo reprodutor</li>\n					<li>Adicione um nome e descri&ccedil;&atilde;o ao reprodutor</li>\n					<li>Adicione um espet&aacute;culo</li>\n					<li>Crie uma programa&ccedil;&atilde;o</li>\n				</ul>\n				<p><a href=\"#createplayer\" id=\"tour3_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Clique aqui para ver o v&iacute;deo</a></p>\n				<p>&nbsp;</p>\n			</div>\n			<div class=\"rColumn\">\n				<img src=\"images/tour/installdemoplayer.png\" class=\"tourimage\" width=\"300\" height=\"105\" alt=\"Install Demo Player\" />\n				<span class=\"homeH1\">Rode a demonstra&ccedil;&atilde;o</span>\n				<p>Se voc&ecirc; quiser, pode instalar a aplica&ccedil;&atilde;o do s3mer e rodar uma demonstra&ccedil;&atilde;o que mostra algumas das caracter&iacute;sticas do s3mer.</p>\n				<p><span class=\"homeH2\">Passos para instalar a aplica&ccedil;&atilde;o</span></p>\n				<ul>\n					<li>Ir a <a href=\"index.php\">http://www.s3mer.com</a></li>\n					<li>Clique o bot&atilde;o para novo reprodutor</li>\n					<li>Clique abrir</li>\n					<li>Clique instalar</li>\n					<li>Clique iniciar demo</li>\n				</ul>\n				<p><a href=\"#installdemoplayer\" id=\"tour4_video_modal\"><img src=\"images/icons/film.png\" width=\"16\" height=\"16\" alt=\"Film\" />&nbsp;Clique aqui para ver o v&iacute;deo</a></p>\n			</div>\n		</div>'),(340,'es','City','Ciudad'),(341,'en','BuyEmail','Thank you for buying your new pro player(s) with us'),(342,'en','EditSubscriptionStatement','Please select the players you want to remove from the list below and hit edit. All changes are final.'),(343,'English','Homepage2','<div class=\"hWrapper\">\n	<div class=\"cBanner homeBig\" style=\"height:57px;padding-top:10px;background-image: url(images/newhome/signupbanner.png);background-repeat: no-repeat;\">\n		<div style=\"text-align: center\"><a href=\"register.php\">Sign-up Free</a> or <a href=\"tour.php\">Take a Tour</a></div>\n	</div>\n	<div class=\"lColumn\">\n		<img src=\"images/monitor2.png\" width=\"300\" height=\"350\" alt=\"Monitor2\" /><br />\n	</div>\n	<div class=\"rColumn\">\n		<p><span class=\"homeH1\">What is s3mer?</span></p>\n		<p>s3mer is a complete dynamic <a href=\"http://en.wikipedia.org/wiki/Digital_signage\">digital signage</a> solution designed to be easy to use, cross platform and feature rich.</p>\n		<br />\n		<p><span class=\"homeH2\">Some of the features of s3mer:</span></p>\n		<ul>\n			<li>Runs Mac OS X, Windows XP, Vista &amp; Linux</li>\n			<li>HD video playback</li>\n			<li>MPEG4 and FLV video format support</li>\n			<li>Flash SWF animation support</li>\n			<li><a href=\"http://en.wikipedia.org/wiki/Rss\">RSS</a> Support</li>\n			<li><a href=\"http://en.wikipedia.org/wiki/Video_podcast\">Video Podcast</a> Enabled</li>\n			<li>PNG, JPEG &amp; GIF image format support</li>\n		</ul>\n		<p><img src=\"images/newhome/otherlogos.png\" width=\"325\" height=\"81\" alt=\"Otherlogos\" /></p>\n	</div>\n	<!-- aligner --><div class=\"cBanner\"></div>\n	<div class=\"lColumn\">\n		<span class=\"homeH1\">The s3mer Demo</span>\n		<p>If you want to test s3mer you can <a href=\"register.php\">sign-up</a> for a free account or you can <span class=\"hHilite\">install the s3mer Player Application and see a demo show.</span></p><br />\n		<p style=\"font-weight:bold\">To Install click on the Install Now Button or follow the instrucition on the manual installation procedure below.</p>\n	</div>\n	<div class=\"rColumn\" align=\"right\" style=\"height:224px\"><br />\n\n 			<iframe src=\"badge/index.html\" frameborder=\"0\" scrolling=\"no\" height=\"190\"></iframe>\n\n	</div>\n	<div class=\"cBanner\" style=\"height:167px;padding-top:10px;background-image: url(images/newhome/manualbanner.png);background-repeat: no-repeat;\">\n		<span class=\"homeH1\" style=\"margin-left:15px;position:relative;top:-5px\">Manual Installation</span>\n	<div align=\"center\"><p style=\"width:96%;text-align:left\">The s3mer Player Application is based on the <a href=\"http://www.adobe.com/products/air/\">Adobe&reg; AIR&trade;</a> runtime. In order to install the player you need to first install the runtime from Adobe and then download and install the s3mer Player Application Package.</p></div><br />\n		<div class=\"lColumn\" style=\"text-align:center\">\n			<a href=\"http://get.adobe.com/air/otherversions/\">Download Adobe&reg; AIR&trade; Runtime</a>\n		</div>\n		<div class=\"rColumn\" style=\"text-align:center\">\n			<a href=\"http://media1.s3mer.com/app/S3mer_1002.air\">Download s3mer PlayerApp</a>\n		</div>\n	</div>\n	<div class=\"lColumn\">\n		<span class=\"homeH1\">Tutorial Video</span>\n		<p>Before you start taking advantage of s3mer we recommend that you watch our tutorial video. This video will help you setup your account and create your first show. Click on the video below to watch or <a href=\"tour.php\">Take a Tour</a>.</p><br />\n		<p>Press the&nbsp;&nbsp;<img src=\"images/newhome/fullscreen.png\" alt=\"Fullscreen\"/>&nbsp;&nbsp;icon to view in full screen</p>\n		<embed src=\"http://blip.tv/play/41643iIA\" type=\"application/x-shockwave-flash\" width=\"313\" height=\"209\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>\n	</div>\n	<div class=\"rColumn\">\n		<span class=\"homeH1\">Tips, Tricks &amp; Updates</span>\n		<p>To stay up to date on new features, tips and tricks related to s3mer please visit <a href=\"http://s3mer.tumblr.com/\">our blog</a>.</p><br />\n		<p style=\"font-weight:bold\"><a href=\"http://s3mer.tumblr.com/\">Click here for The s3mer blog</a></p><br />\n		<span class=\"homeH1\">Newsletter</span>\n		<p>Please leave your name and email if you wish to recieve our newsletter.</p>\n		<div class=\"hNewsletter\">\n			<form action=\"http://s3merinc.cmail1.com/s/337333/\" method=\"post\">\n			<div>\n			<label for=\"name\">Name:</label><br /><input type=\"text\" name=\"name\" id=\"name\" /><br />\n			<label for=\"l337333-337333\">Email:</label><br /><input type=\"text\" name=\"cm-337333-337333\" id=\"l337333-337333\" /><br />\n			<div class=\"buttons\">\n				<button type=\"submit\">\n					<img alt=\"\" src=\"images/icons/email_add.png\"/> \n					Subscribe\n				</button>\n			</div>\n			</div>\n			</form>\n		</div>\n	</div>\n	<div class=\"cBanner\" style=\"height:130px;padding-top:10px;background-image: url(images/newhome/feedbackbanner.png);background-repeat: no-repeat;margin-top:20px\">\n		<span class=\"homeH1\" style=\"margin-left:15px;position:relative;top:-5px\">Feedback</span>\n		<div align=\"center\"><p style=\"width:96%;text-align:left\">We want to know what you think about our product. Please write your commets to <a href=\"mailto:feedback@s3mer.com\">feedback@s3mer.com</a>. If you expirience and issue using s3mer or if you want to report a bug you can check out our <a href=\"http://groups.google.com/group/s3mer\">discussion group</a>, you can chat with one of our support technicians or you can write an email to <a href=\"mailto:support@s3mer.com\">support@s3mer.com</a></p></div>\n	</div>\n</div>'),(348,'es','New Pro Player','Nuevo Reproductor Pro'),(347,'es','New Free Player','Nuevo Reproductor'),(349,'es','Click here to create a new free player','Haga click aqu&iacute; para crear un nuevo reproductor gratuito'),(350,'es','Click here to create a new pro player','Haga click aqu&iacute; para crear un nuevo reproductor pro'),(351,'es','New Free Show','Nuevo Espect&aacute;culo'),(352,'es','s3mer Pro Player','Reproductor s3mer Pro'),(353,'es','Get','Comprar'),(354,'es','Pro Players','Reproductores Pro'),(355,'es','each month per Pro Player','cada mes por cada reproductor pro'),(374,'es','Total Players','Reproductores Totales'),(375,'es','Total Monthly Amount','Cantidad Total Mensual');
/*!40000 ALTER TABLE `contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(45) NOT NULL,
  `timeformat` int(11) DEFAULT NULL,
  `dateformat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=276 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'Puerto Rico',NULL,NULL),(2,'United States',NULL,NULL),(3,'República Dominicana',NULL,NULL),(4,'Colombia',NULL,NULL),(5,'Ecuador',NULL,NULL),(6,'Brasil',NULL,NULL),(7,'Panamá',NULL,NULL),(8,'Honduras',NULL,NULL),(9,'México',NULL,NULL),(10,'Canada',NULL,NULL),(11,'France',NULL,NULL),(12,'España',NULL,NULL),(13,'Italia',NULL,NULL),(14,'Trinidad and Tobago',NULL,NULL),(15,'Aruba',NULL,NULL),(16,'Venezuela',NULL,NULL),(17,'Bonaire',NULL,NULL),(18,'Curaçao',NULL,NULL),(19,'Argentina',NULL,NULL),(20,'Chile',NULL,NULL),(21,'Bolivia',NULL,NULL),(22,'Perú',NULL,NULL),(24,'Portugal',NULL,NULL),(25,'China',NULL,NULL),(26,'India',NULL,NULL),(27,'Australia',NULL,NULL),(28,'South Africa',NULL,NULL),(29,'Holland',NULL,NULL),(30,'Norway',NULL,NULL),(31,'Turkey',NULL,NULL),(32,'Malaysia',NULL,NULL),(33,'Germany',NULL,NULL),(34,'United Kingdom',NULL,NULL),(35,'Croatia',NULL,NULL),(36,'Austria',NULL,NULL),(37,'New Zealand',NULL,NULL),(38,'Bosnia and Herzegovina',NULL,NULL),(39,'Russia',NULL,NULL),(41,'Afghanistan',NULL,NULL),(42,'Aland',NULL,NULL),(43,'Albania',NULL,NULL),(44,'Algeria',NULL,NULL),(45,'American Samoa',NULL,NULL),(46,'Andorra',NULL,NULL),(47,'Angola',NULL,NULL),(48,'Anguilla',NULL,NULL),(49,'Antarctica',NULL,NULL),(50,'Antigua and Barbuda',NULL,NULL),(51,'Armenia',NULL,NULL),(52,'Ascension',NULL,NULL),(53,'Ashmore and Cartier Islands',NULL,NULL),(54,'Australian Antarctic Territory',NULL,NULL),(55,'Azerbaijan',NULL,NULL),(56,'Bahamas, The',NULL,NULL),(57,'Bahrain',NULL,NULL),(58,'Baker Island',NULL,NULL),(59,'Bangladesh',NULL,NULL),(60,'Barbados',NULL,NULL),(61,'Belarus',NULL,NULL),(62,'Belgium',NULL,NULL),(63,'Belize',NULL,NULL),(64,'Benin',NULL,NULL),(65,'Bermuda',NULL,NULL),(66,'Bhutan',NULL,NULL),(67,'Botswana',NULL,NULL),(68,'Bouvet Island',NULL,NULL),(69,'British Antarctic Territory',NULL,NULL),(70,'British Indian Ocean Territory',NULL,NULL),(71,'British Sovereign Base Areas',NULL,NULL),(72,'British Virgin Islands',NULL,NULL),(73,'Brunei',NULL,NULL),(74,'Bulgaria',NULL,NULL),(75,'Burkina Faso',NULL,NULL),(76,'Burundi',NULL,NULL),(77,'Cambodia',NULL,NULL),(78,'Cameroon',NULL,NULL),(79,'Cape Verde',NULL,NULL),(80,'Cayman Islands',NULL,NULL),(81,'Central African Republic',NULL,NULL),(82,'Chad',NULL,NULL),(83,'Christmas Island',NULL,NULL),(84,'Clipperton Island',NULL,NULL),(85,'Cocos (Keeling) Islands',NULL,NULL),(86,'Comoros',NULL,NULL),(87,'Congo',NULL,NULL),(88,'Cook Islands',NULL,NULL),(89,'Coral Sea Islands',NULL,NULL),(90,'Costa Rica',NULL,NULL),(91,'Cote d\'Ivoire (Ivory Coast)',NULL,NULL),(92,'Cuba',NULL,NULL),(93,'Cyprus',NULL,NULL),(94,'Czech Republic',NULL,NULL),(95,'Denmark',NULL,NULL),(96,'Djibouti',NULL,NULL),(97,'Dominica',NULL,NULL),(98,'Egypt',NULL,NULL),(99,'El Salvador',NULL,NULL),(100,'Equatorial Guinea',NULL,NULL),(101,'Eritrea',NULL,NULL),(102,'Estonia',NULL,NULL),(103,'Ethiopia',NULL,NULL),(104,'Falkland Islands (Islas Malvinas)',NULL,NULL),(105,'Faroe Islands',NULL,NULL),(106,'Fiji',NULL,NULL),(107,'Finland',NULL,NULL),(108,'French Guiana',NULL,NULL),(109,'French Polynesia',NULL,NULL),(110,'French Southern and Antarctic Lands',NULL,NULL),(111,'Gabon',NULL,NULL),(112,'Georgia',NULL,NULL),(113,'Ghana',NULL,NULL),(114,'Gibraltar',NULL,NULL),(115,'Greece',NULL,NULL),(116,'Greenland',NULL,NULL),(117,'Grenada',NULL,NULL),(118,'Guadeloupe',NULL,NULL),(119,'Guam',NULL,NULL),(120,'Guatemala',NULL,NULL),(121,'Guernsey',NULL,NULL),(122,'Guinea',NULL,NULL),(123,'Guinea-Bissau',NULL,NULL),(124,'Guyana',NULL,NULL),(125,'Haiti',NULL,NULL),(126,'Heard Island and McDonald Islands',NULL,NULL),(127,'Hong Kong',NULL,NULL),(128,'Howland Island',NULL,NULL),(129,'Hungary',NULL,NULL),(131,'Indonesia',NULL,NULL),(132,'Iran',NULL,NULL),(133,'Iraq',NULL,NULL),(134,'Ireland',NULL,NULL),(135,'Isle of Man',NULL,NULL),(136,'Israel',NULL,NULL),(137,'Jamaica',NULL,NULL),(138,'Japan',NULL,NULL),(139,'Jarvis Island',NULL,NULL),(140,'Jersey',NULL,NULL),(141,'Johnston Atoll',NULL,NULL),(142,'Jordan',NULL,NULL),(143,'Kazakhstan',NULL,NULL),(144,'Kenya',NULL,NULL),(145,'Kingman Reef',NULL,NULL),(146,'Kiribati',NULL,NULL),(147,'Korea, (North Korea)',NULL,NULL),(148,'Korea, (South Korea)',NULL,NULL),(149,'Kosovo',NULL,NULL),(150,'Kuwait',NULL,NULL),(151,'Kyrgyzstan',NULL,NULL),(152,'Laos',NULL,NULL),(153,'Latvia',NULL,NULL),(154,'Lebanon',NULL,NULL),(155,'Lesotho',NULL,NULL),(156,'Liberia',NULL,NULL),(157,'Libya',NULL,NULL),(158,'Liechtenstein',NULL,NULL),(159,'Lithuania',NULL,NULL),(160,'Luxembourg',NULL,NULL),(161,'Macau',NULL,NULL),(162,'Macedonia',NULL,NULL),(163,'Madagascar',NULL,NULL),(164,'Malawi',NULL,NULL),(165,'Maldives',NULL,NULL),(166,'Mali',NULL,NULL),(167,'Malta',NULL,NULL),(168,'Marshall Islands',NULL,NULL),(169,'Martinique',NULL,NULL),(170,'Mauritania',NULL,NULL),(171,'Mauritius',NULL,NULL),(172,'Mayotte',NULL,NULL),(173,'Micronesia',NULL,NULL),(174,'Midway Islands',NULL,NULL),(175,'Moldova',NULL,NULL),(176,'Monaco',NULL,NULL),(177,'Mongolia',NULL,NULL),(178,'Montenegro',NULL,NULL),(179,'Montserrat',NULL,NULL),(180,'Morocco',NULL,NULL),(181,'Mozambique',NULL,NULL),(182,'Myanmar (Burma)',NULL,NULL),(183,'Nagorno-Karabakh',NULL,NULL),(184,'Namibia',NULL,NULL),(185,'Nauru',NULL,NULL),(186,'Navassa Island',NULL,NULL),(187,'Nepal',NULL,NULL),(188,'Netherlands',NULL,NULL),(189,'Netherlands Antilles',NULL,NULL),(190,'New Caledonia',NULL,NULL),(191,'Nicaragua',NULL,NULL),(192,'Niger',NULL,NULL),(193,'Nigeria',NULL,NULL),(194,'Niue',NULL,NULL),(195,'Norfolk Island',NULL,NULL),(196,'Northern Cyprus',NULL,NULL),(197,'Northern Mariana Islands',NULL,NULL),(198,'Oman',NULL,NULL),(199,'Pakistan',NULL,NULL),(200,'Palau',NULL,NULL),(201,'Palestinian Territories',NULL,NULL),(202,'Palmyra Atoll',NULL,NULL),(203,'Papua New Guinea',NULL,NULL),(204,'Paraguay',NULL,NULL),(205,'Peter I Island',NULL,NULL),(206,'Philippines',NULL,NULL),(207,'Pitcairn Islands',NULL,NULL),(208,'Poland',NULL,NULL),(209,'Pridnestrovie (Transnistria)',NULL,NULL),(210,'Qatar',NULL,NULL),(211,'Queen Maud Land',NULL,NULL),(212,'Reunion',NULL,NULL),(213,'Romania',NULL,NULL),(214,'Ross Dependency',NULL,NULL),(215,'Rwanda',NULL,NULL),(216,'Saint Barthelemy',NULL,NULL),(217,'Saint Helena',NULL,NULL),(218,'Saint Kitts and Nevis',NULL,NULL),(219,'Saint Lucia',NULL,NULL),(220,'Saint Martin',NULL,NULL),(221,'Saint Pierre and Miquelon',NULL,NULL),(222,'Saint Vincent and the Grenadines',NULL,NULL),(223,'Samoa',NULL,NULL),(224,'San Marino',NULL,NULL),(225,'Sao Tome and Principe',NULL,NULL),(226,'Saudi Arabia',NULL,NULL),(227,'Senegal',NULL,NULL),(228,'Serbia',NULL,NULL),(229,'Seychelles',NULL,NULL),(230,'Sierra Leone',NULL,NULL),(231,'Singapore',NULL,NULL),(232,'Slovakia',NULL,NULL),(233,'Slovenia',NULL,NULL),(234,'Solomon Islands',NULL,NULL),(235,'Somalia',NULL,NULL),(236,'Somaliland',NULL,NULL),(237,'South Georgia',NULL,NULL),(238,'South Ossetia',NULL,NULL),(239,'Sri Lanka',NULL,NULL),(240,'Sudan',NULL,NULL),(241,'Suriname',NULL,NULL),(242,'Svalbard',NULL,NULL),(243,'Swaziland',NULL,NULL),(244,'Sweden',NULL,NULL),(245,'Switzerland',NULL,NULL),(246,'Syria',NULL,NULL),(247,'Taiwan',NULL,NULL),(248,'Tajikistan',NULL,NULL),(249,'Tanzania',NULL,NULL),(250,'Thailand',NULL,NULL),(251,'The Gambia',NULL,NULL),(252,'Timor-Leste (East Timor)',NULL,NULL),(253,'Togo',NULL,NULL),(254,'Tokelau',NULL,NULL),(255,'Tonga',NULL,NULL),(256,'Tristan da Cunha',NULL,NULL),(257,'Tunisia',NULL,NULL),(258,'Turkmenistan',NULL,NULL),(259,'Turks and Caicos Islands',NULL,NULL),(260,'Tuvalu',NULL,NULL),(261,'U.S. Virgin Islands',NULL,NULL),(262,'Uganda',NULL,NULL),(263,'Ukraine',NULL,NULL),(264,'United Arab Emirates',NULL,NULL),(265,'Uruguay',NULL,NULL),(266,'Uzbekistan',NULL,NULL),(267,'Vanuatu',NULL,NULL),(268,'Vatican City',NULL,NULL),(269,'Vietnam',NULL,NULL),(270,'Wake Island',NULL,NULL),(271,'Wallis and Futuna',NULL,NULL),(272,'Western Sahara',NULL,NULL),(273,'Yemen',NULL,NULL),(274,'Zambia',NULL,NULL),(275,'Zimbabwe',NULL,NULL);
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countriesbackup`
--

DROP TABLE IF EXISTS `countriesbackup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countriesbackup` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `country` varchar(45) NOT NULL,
  `timeformat` int(11) DEFAULT NULL,
  `dateformat` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countriesbackup`
--

LOCK TABLES `countriesbackup` WRITE;
/*!40000 ALTER TABLE `countriesbackup` DISABLE KEYS */;
INSERT INTO `countriesbackup` VALUES (1,'Puerto Rico',NULL,NULL),(2,'United States',NULL,NULL),(3,'República Dominicana',NULL,NULL),(4,'Colombia',NULL,NULL),(5,'Ecuador',NULL,NULL),(6,'Brasil',NULL,NULL),(7,'Panamá',NULL,NULL),(8,'Honduras',NULL,NULL),(9,'México',NULL,NULL),(10,'Canada',NULL,NULL),(11,'France',NULL,NULL),(12,'España',NULL,NULL),(13,'Italia',NULL,NULL),(14,'Trinidad and Tobago',NULL,NULL),(15,'Aruba',NULL,NULL),(16,'Venezuela',NULL,NULL),(17,'Bonaire',NULL,NULL),(18,'Curaçao',NULL,NULL),(19,'Argentina',NULL,NULL),(20,'Chile',NULL,NULL),(21,'Bolivia',NULL,NULL),(22,'Perú',NULL,NULL),(24,'Portugal',NULL,NULL),(25,'China',NULL,NULL),(26,'India',NULL,NULL),(27,'Australia',NULL,NULL),(28,'South Africa',NULL,NULL),(29,'Holland',NULL,NULL),(30,'Norway',NULL,NULL),(31,'Turkey',NULL,NULL),(32,'Malaysia',NULL,NULL),(33,'Germany',NULL,NULL),(34,'United Kingdom',NULL,NULL),(35,'Croatia',NULL,NULL),(36,'Austria',NULL,NULL),(37,'New Zealand',NULL,NULL),(38,'Bosnia and Herzegovina',NULL,NULL),(39,'Russia',NULL,NULL),(40,'',NULL,NULL);
/*!40000 ALTER TABLE `countriesbackup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dateformats`
--

DROP TABLE IF EXISTS `dateformats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dateformats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `format` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dateformats`
--

LOCK TABLES `dateformats` WRITE;
/*!40000 ALTER TABLE `dateformats` DISABLE KEYS */;
INSERT INTO `dateformats` VALUES (1,'MM-DD-YYYY'),(2,'DD-MM-YYYY'),(3,'YYYY-MM-DD');
/*!40000 ALTER TABLE `dateformats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dayformats`
--

DROP TABLE IF EXISTS `dayformats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dayformats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `format` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dayformats`
--

LOCK TABLES `dayformats` WRITE;
/*!40000 ALTER TABLE `dayformats` DISABLE KEYS */;
INSERT INTO `dayformats` VALUES (1,'mm-dd-yyyy'),(2,'dd-mm-yyyy'),(3,'yyyy-mm-dd');
/*!40000 ALTER TABLE `dayformats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `effects`
--

DROP TABLE IF EXISTS `effects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `effects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `effecten` varchar(50) NOT NULL,
  `effectes` varchar(50) NOT NULL,
  `effectpt` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `effects`
--

LOCK TABLES `effects` WRITE;
/*!40000 ALTER TABLE `effects` DISABLE KEYS */;
INSERT INTO `effects` VALUES (1,'Dissolve','Disolver','Disolver'),(2,'Cut','Cortar','Cortar');
/*!40000 ALTER TABLE `effects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qen` varchar(200) DEFAULT NULL,
  `qes` varchar(200) DEFAULT NULL,
  `qpt` varchar(200) DEFAULT NULL,
  `ansen` text,
  `anses` text,
  `anspt` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faq`
--

LOCK TABLES `faq` WRITE;
/*!40000 ALTER TABLE `faq` DISABLE KEYS */;
INSERT INTO `faq` VALUES (1,'What is s3mer?','Qué es s3mer?','Que é o s3mer?','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Quisque tortor tellus, ultricies eget, bibendum nec, elementum eget, felis. Morbi blandit, urna quis vulputate interdum, felis felis vulputate quam, et euismod est augue nec eros. Cras sodales, mi vitae pellentesque pellentesque, lacus metus laoreet arcu, a cursus leo magna a velit. Suspendisse scelerisque, odio sit amet semper commodo, felis orci sodales est, eget malesuada augue nunc lobortis nulla. Praesent eleifend felis. Nulla interdum porttitor risus. Maecenas iaculis luctus metus. Proin quam felis, mattis eu, imperdiet eget, mattis a, ipsum. Quisque ac magna at urna mattis adipiscing. Nullam a felis. Suspendisse ni   EN','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Quisque tortor tellus, ultricies eget, bibendum nec, elementum eget, felis. Morbi blandit, urna quis vulputate interdum, felis felis vulputate quam, et euismod est augue nec eros. Cras sodales, mi vitae pellentesque pellentesque, lacus metus laoreet arcu, a cursus leo magna a velit. Suspendisse scelerisque, odio sit amet semper commodo, felis orci sodales est, eget malesuada augue nunc lobortis nulla. Praesent eleifend felis. Nulla interdum porttitor risus. Maecenas iaculis luctus metus. Proin quam felis, mattis eu, imperdiet eget, mattis a, ipsum. Quisque ac magna at urna mattis adipiscing. Nullam a felis. Suspendisse ni ES','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Quisque tortor tellus, ultricies eget, bibendum nec, elementum eget, felis. Morbi blandit, urna quis vulputate interdum, felis felis vulputate quam, et euismod est augue nec eros. Cras sodales, mi vitae pellentesque pellentesque, lacus metus laoreet arcu, a cursus leo magna a velit. Suspendisse scelerisque, odio sit amet semper commodo, felis orci sodales est, eget malesuada augue nunc lobortis nulla. Praesent eleifend felis. Nulla interdum porttitor risus. Maecenas iaculis luctus metus. Proin quam felis, mattis eu, imperdiet eget, mattis a, ipsum. Quisque ac magna at urna mattis adipiscing. Nullam a felis. Suspendisse ni PT'),(2,'What s3mer is good for?','Para qué sirve s3mer?',NULL,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Quisque tortor tellus, ultricies eget, bibendum nec, elementum eget, felis. Morbi blandit, urna quis vulputate interdum, felis felis vulputate quam, et euismod est augue nec eros. Cras sodales, mi vitae pellentesque pellentesque, lacus metus laoreet arcu, a cursus leo magna a velit. Suspendisse scelerisque, odio sit amet semper commodo, felis orci sodales est, eget malesuada augue nunc lobortis nulla. Praesent eleifend felis. Nulla interdum porttitor risus. Maecenas iaculis luctus metus. Proin quam felis, mattis eu, imperdiet eget, mattis a, ipsum. Quisque ac magna at urna mattis adipiscing. Nullam a felis. Suspendisse ni   EN','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Quisque tortor tellus, ultricies eget, bibendum nec, elementum eget, felis. Morbi blandit, urna quis vulputate interdum, felis felis vulputate quam, et euismod est augue nec eros. Cras sodales, mi vitae pellentesque pellentesque, lacus metus laoreet arcu, a cursus leo magna a velit. Suspendisse scelerisque, odio sit amet semper commodo, felis orci sodales est, eget malesuada augue nunc lobortis nulla. Praesent eleifend felis. Nulla interdum porttitor risus. Maecenas iaculis luctus metus. Proin quam felis, mattis eu, imperdiet eget, mattis a, ipsum. Quisque ac magna at urna mattis adipiscing. Nullam a felis. Suspendisse ni   Es','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Quisque tortor tellus, ultricies eget, bibendum nec, elementum eget, felis. Morbi blandit, urna quis vulputate interdum, felis felis vulputate quam, et euismod est augue nec eros. Cras sodales, mi vitae pellentesque pellentesque, lacus metus laoreet arcu, a cursus leo magna a velit. Suspendisse scelerisque, odio sit amet semper commodo, felis orci sodales est, eget malesuada augue nunc lobortis nulla. Praesent eleifend felis. Nulla interdum porttitor risus. Maecenas iaculis luctus metus. Proin quam felis, mattis eu, imperdiet eget, mattis a, ipsum. Quisque ac magna at urna mattis adipiscing. Nullam a felis. Suspendisse ni   pt'),(3,'Who needs the Pro Version?','Quién necesita la versión PRO?',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fileextensions`
--

DROP TABLE IF EXISTS `fileextensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fileextensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(10) NOT NULL DEFAULT '',
  `mediatype` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fileextensions`
--

LOCK TABLES `fileextensions` WRITE;
/*!40000 ALTER TABLE `fileextensions` DISABLE KEYS */;
INSERT INTO `fileextensions` VALUES (1,'jpg',2),(2,'png',2),(3,'gif',2),(4,'swf',3),(5,'mov',1),(6,'m4v',1),(7,'flv',1),(8,'mp4',1);
/*!40000 ALTER TABLE `fileextensions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(40) DEFAULT NULL,
  `language` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'en','English'),(2,'es','Español'),(4,'pt','Português');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `layout`
--

DROP TABLE IF EXISTS `layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `layout` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `layoutname` varchar(45) NOT NULL,
  `createdon` datetime NOT NULL,
  `resx` int(10) unsigned NOT NULL,
  `resy` int(10) unsigned NOT NULL,
  `description` varchar(45) NOT NULL,
  `imagefile` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `layout`
--

LOCK TABLES `layout` WRITE;
/*!40000 ALTER TABLE `layout` DISABLE KEYS */;
INSERT INTO `layout` VALUES (1,'Test Layout','0000-00-00 00:00:00',1280,720,'Test Layout 1 720p','layout1.png'),(2,'Layout 1','0000-00-00 00:00:00',1280,720,'Layout 1 720p','layout2.png'),(3,'Layout 2','0000-00-00 00:00:00',1280,720,'Layout 2 720p','layout3.png'),(4,'Layout 3','0000-00-00 00:00:00',1280,720,'Layout 3 720p','layout4.png'),(5,'Layout 4','0000-00-00 00:00:00',1280,720,'Layout 4 720p','layout5.png'),(6,'Layout 6','0000-00-00 00:00:00',1280,720,'Layout 7 720p','layout6.png'),(7,'Layout 7','0000-00-00 00:00:00',1280,720,'Layout 7 720p','layout7.png'),(8,'Layout 8','0000-00-00 00:00:00',1280,720,'Layout 8 720p','layout8.png'),(9,'Layout9','0000-00-00 00:00:00',1280,720,'Layout 9 720p','layout9.png'),(10,'Layout 10','0000-00-00 00:00:00',1280,720,'Layout 9 720p','layout10.png');
/*!40000 ALTER TABLE `layout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `layoutregion`
--

DROP TABLE IF EXISTS `layoutregion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `layoutregion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `layoutid` int(10) unsigned NOT NULL,
  `regionname` varchar(45) NOT NULL,
  `regiontype` int(10) unsigned NOT NULL,
  `x` int(10) unsigned NOT NULL,
  `y` int(10) unsigned NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `left` int(10) NOT NULL DEFAULT '0',
  `top` int(10) NOT NULL DEFAULT '0',
  `webwidth` int(10) NOT NULL DEFAULT '0',
  `webheight` int(10) NOT NULL DEFAULT '0',
  `reserved` int(1) NOT NULL DEFAULT '0',
  `zindex` int(11) NOT NULL,
  `rsscolor` varchar(6) NOT NULL,
  `url` varchar(255) NOT NULL,
  `mainmedia` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `layoutregion`
--

LOCK TABLES `layoutregion` WRITE;
/*!40000 ALTER TABLE `layoutregion` DISABLE KEYS */;
INSERT INTO `layoutregion` VALUES (1,1,'RSS1',2,12,0,971,82,0,0,145,15,0,1,'FFFFFF','',0),(2,1,'Main Media',1,12,82,971,546,0,0,145,74,0,1,'','',1),(4,1,'Side Bar',3,994,0,285,639,0,-97,55,93,0,1,'','',0),(5,1,'Clock',4,994,639,251,81,149,-97,55,15,1,1,'','',0),(3,1,'RSS2',2,12,641,971,81,0,0,145,15,0,1,'000000','',0),(7,2,'Main Media',1,0,0,1280,639,0,0,204,93,0,1,'','',1),(8,2,'RSS1',2,12,641,971,81,0,0,145,15,0,1,'000000','',0),(11,2,'Clock',4,994,639,251,81,0,0,55,15,1,1,'','',0),(12,3,'Main Media',1,0,0,995,720,0,0,145,112,0,1,'','',1),(13,3,'Side Bar',3,994,0,285,639,0,0,55,93,0,1,'','',0),(14,4,'Main Media',1,0,0,1280,720,0,0,204,112,0,0,'','',1),(15,4,'Clock',4,994,639,251,81,149,-19,55,15,1,2,'','',0),(16,3,'Clock',4,994,639,251,81,0,0,55,15,1,1,'','',0),(17,5,'Side Bar',3,0,0,285,639,0,0,55,93,0,1,'','',0),(18,5,'Clock',4,36,639,251,81,-59,97,55,15,1,1,'','',0),(19,5,'RSS1',2,285,0,971,82,0,-19,145,15,0,1,'FFFFFF','',0),(20,5,'Main Media',1,297,82,971,546,0,-19,145,74,0,1,'','',1),(21,5,'RSS2',2,285,641,971,81,59,-19,145,15,0,1,'000000','',0),(35,7,'Clock',4,36,639,251,81,0,0,55,15,1,1,'','',0),(33,6,'Main Media',1,285,0,995,720,0,-19,145,112,0,1,'','',1),(34,7,'Main Media',1,0,0,1280,639,0,0,204,93,0,1,'','',1),(31,6,'Clock',4,36,639,251,81,-59,97,55,15,1,1,'','',0),(30,6,'Side Bar',3,0,0,285,639,0,0,55,93,0,1,'','',0),(28,8,'Main Media',1,0,0,1280,720,0,0,204,112,0,0,'','',1),(29,8,'Clock',4,36,639,251,81,0,-19,55,15,1,2,'','',0),(36,7,'RSS1',2,280,639,971,81,0,0,145,115,0,1,'000000','',0),(38,1,'Background',5,0,0,1280,720,0,0,0,0,0,0,'','http://media1.s3mer.com/app/layoutbkg/layout001.png',0),(39,2,'Background',5,0,0,1280,720,0,0,0,0,0,0,'','http://media1.s3mer.com/app/layoutbkg/layout002.png',0),(40,3,'Background',5,0,0,1280,720,0,0,0,0,0,0,'','http://media1.s3mer.com/app/layoutbkg/layout003.png',0),(41,4,'Background',5,980,624,300,96,0,0,0,0,0,1,'','http://media1.s3mer.com/app/layoutbkg/layout004.png',0),(42,5,'Background',5,0,0,1280,720,0,0,0,0,0,0,'','http://media1.s3mer.com/app/layoutbkg/layout005.png',0),(43,6,'Background',5,0,0,1280,720,0,0,0,0,0,0,'','http://media1.s3mer.com/app/layoutbkg/layout006.png',0),(44,7,'Background',5,0,0,1280,720,0,0,0,0,0,0,'','http://media1.s3mer.com/app/layoutbkg/layout007.png',0),(45,8,'Background',5,0,624,300,96,0,0,0,0,0,1,'','http://media1.s3mer.com/app/layoutbkg/layout008.png',0),(46,9,'Top Image',1,0,0,995,82,0,0,145,15,0,1,'FFFFFF','',0),(47,9,'Main Media',1,12,94,971,534,0,0,145,74,0,1,'','',1),(48,9,'Bottom Image',1,0,639,995,82,0,0,145,15,0,1,'000000','',0),(49,9,'Side Bar',3,994,0,285,639,0,-97,55,93,0,1,'','',0),(50,9,'Clock',4,994,639,251,81,149,-97,55,15,1,1,'','',0),(51,9,'Background',5,0,0,1280,720,0,0,0,0,0,0,'','http://media1.s3mer.com/app/layoutbkg/layout001.png',0),(52,10,'Main Media',1,0,0,995,639,0,0,145,93,0,1,'','',1),(53,10,'RSS',2,12,641,971,81,0,0,145,15,0,1,'000000','',0),(54,10,'Clock',4,994,639,251,81,0,0,55,15,1,1,'','',0),(55,10,'Sidebar',3,994,0,285,639,149,-116,55,93,0,1,'','',0),(56,10,'Background',5,0,0,1280,720,0,0,0,0,0,0,'','http://media1.s3mer.com/app/layoutbkg/layout001.png',0);
/*!40000 ALTER TABLE `layoutregion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ownerid` int(10) unsigned NOT NULL,
  `postedon` datetime NOT NULL,
  `filename` text NOT NULL,
  `mediatype` int(10) unsigned NOT NULL,
  `shared` int(10) unsigned NOT NULL,
  `description` varchar(200) NOT NULL,
  `name` text NOT NULL,
  `filesize` int(11) NOT NULL DEFAULT '0',
  `deleteflag` int(1) NOT NULL DEFAULT '0',
  `thumbnailstate` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mediafolders`
--

DROP TABLE IF EXISTS `mediafolders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mediafolders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folder` varchar(50) DEFAULT NULL,
  `owner` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mediafolders`
--

LOCK TABLES `mediafolders` WRITE;
/*!40000 ALTER TABLE `mediafolders` DISABLE KEYS */;
/*!40000 ALTER TABLE `mediafolders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mediatype`
--

DROP TABLE IF EXISTS `mediatype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mediatype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mediatype` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mediatype`
--

LOCK TABLES `mediatype` WRITE;
/*!40000 ALTER TABLE `mediatype` DISABLE KEYS */;
INSERT INTO `mediatype` VALUES (1,'video'),(2,'image'),(3,'swf'),(4,'rss'),(5,'timedate');
/*!40000 ALTER TABLE `mediatype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menuid` varchar(50) DEFAULT NULL,
  `es` varchar(50) DEFAULT NULL,
  `en` varchar(50) DEFAULT NULL,
  `pt` varchar(50) DEFAULT NULL,
  `link` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,'1','documentación','documentation','documentação','http://docs.s3mer.com'),(2,'1','blog','blog','blog','http://blog.s3mer.com'),(3,'1','contáctenos','contact us','contate-nos','contactus.php'),(4,'2','Bienvenido','Welcome','Bem-vindo','index.php'),(5,'2','Inicio de Sesión','Login','Inicio da Sessão','login.php'),(6,'2','Crear cuenta','Sign-up','Criar conta','register.php'),(7,'3','Compañía','Company','Empresa','company.php'),(8,'3','Términos de Uso','Terms of Use','Termos de Uso','termsofuse.php'),(9,'3','Política de Privacidad','Privacy Policy','Política de Privacidade','privacypolicy.php'),(10,'3','Derechos','Copyright','Dereitos','copyright.php'),(11,'4','descargas','downloads','descargas','downloads.php'),(12,'4','opciones','settings','opções','settings.php'),(13,'4','documentación','documentation','documentação','http://docs.s3mer.com'),(14,'4','blog','blog','blog','http://blog.s3mer.com'),(15,'4','contáctenos','contact us','contate-nos','contactus.php'),(16,'5','Reproductores','Players','Reproductores','player-tiles.php'),(17,'5','Espectáculos','Shows','Espetáculos','show-tiles.php'),(18,'5','Contenidos','Library','Biblioteca','library-tiles.php'),(19,'5','Tienda','Store','Loja','store.php');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `months`
--

DROP TABLE IF EXISTS `months`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `months` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `monen` varchar(50) NOT NULL DEFAULT '',
  `mones` varchar(50) NOT NULL DEFAULT '',
  `monpt` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `months`
--

LOCK TABLES `months` WRITE;
/*!40000 ALTER TABLE `months` DISABLE KEYS */;
INSERT INTO `months` VALUES (1,'January','Enero','Janeiro'),(2,'February','Febrero','Fevereiro'),(3,'March','Marzo','Março'),(4,'April','Abril','Abril'),(5,'May','Mayo','Maio'),(6,'June','Junio','Junho'),(7,'July','Julio','Julho'),(8,'August','Agosto','Agosto'),(9,'September','Septiembre','Setembro'),(10,'October','Octubre','Outubro'),(11,'November','Noviembre','Novembro'),(12,'December','Diciembre','Dezembro');
/*!40000 ALTER TABLE `months` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments_received`
--

DROP TABLE IF EXISTS `payments_received`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments_received` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments_received`
--

LOCK TABLES `payments_received` WRITE;
/*!40000 ALTER TABLE `payments_received` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments_received` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paypal_transactions`
--

DROP TABLE IF EXISTS `paypal_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paypal_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mc_gross` decimal(10,2) DEFAULT NULL,
  `address_status` varchar(50) DEFAULT NULL,
  `payer_id` varchar(50) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `address_street` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `charset` varchar(50) DEFAULT NULL,
  `address_zip` varchar(10) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `mc_fee` decimal(10,2) DEFAULT NULL,
  `address_name` varchar(50) DEFAULT NULL,
  `notify_version` varchar(10) DEFAULT NULL,
  `custom` varchar(50) DEFAULT NULL,
  `payer_status` varchar(50) DEFAULT NULL,
  `business` varchar(100) DEFAULT NULL,
  `address_country` varchar(100) DEFAULT NULL,
  `address_city` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `verify_sign` varchar(150) DEFAULT NULL,
  `payer_email` varchar(100) DEFAULT NULL,
  `txn_id` varchar(100) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `address_state` varchar(50) DEFAULT NULL,
  `receiver_email` varchar(50) DEFAULT NULL,
  `payment_fee` decimal(10,2) DEFAULT NULL,
  `receiver_id` varchar(100) DEFAULT NULL,
  `txn_type` varchar(100) DEFAULT NULL,
  `item_name` varchar(150) DEFAULT NULL,
  `mc_currency` varchar(100) DEFAULT NULL,
  `item_number` varchar(50) DEFAULT NULL,
  `residence_country` varchar(50) DEFAULT NULL,
  `test_ipn` int(11) DEFAULT NULL,
  `shipping` decimal(10,2) DEFAULT NULL,
  `protection_eligibility` varchar(50) DEFAULT NULL,
  `address_country_code` varchar(20) DEFAULT NULL,
  `pending_reason` varchar(50) DEFAULT NULL,
  `payment_gross` decimal(10,2) DEFAULT NULL,
  `payer_business_name` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(15) DEFAULT NULL,
  `invoice` varchar(127) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `option_name1` varchar(100) DEFAULT NULL,
  `option_name2` varchar(100) DEFAULT NULL,
  `option_selection1` varchar(100) DEFAULT NULL,
  `option_selection2` varchar(100) DEFAULT NULL,
  `auth_id` varchar(100) DEFAULT NULL,
  `auth_exp` date DEFAULT NULL,
  `auth_status` varchar(30) DEFAULT NULL,
  `parent_txn_id` varchar(20) DEFAULT NULL,
  `reason_code` varchar(50) DEFAULT NULL,
  `remaining_settle` decimal(10,2) DEFAULT NULL,
  `shipping_method` varchar(100) DEFAULT NULL,
  `transaction_entity` varchar(100) DEFAULT NULL,
  `auth_amount` decimal(10,2) DEFAULT NULL,
  `exchange_rate` decimal(10,4) DEFAULT NULL,
  `settle_amount` decimal(10,2) DEFAULT NULL,
  `auction_buyer_id` varchar(100) DEFAULT NULL,
  `auction_closing_date` date DEFAULT NULL,
  `auction_multi_item` int(11) DEFAULT NULL,
  `for_auction` varchar(20) DEFAULT NULL,
  `subscr_date` date DEFAULT NULL,
  `subscr_effective` date DEFAULT NULL,
  `period1` varchar(50) DEFAULT NULL,
  `period2` varchar(50) DEFAULT NULL,
  `period3` varchar(50) DEFAULT NULL,
  `amount1` decimal(10,2) DEFAULT NULL,
  `amount2` decimal(10,2) DEFAULT NULL,
  `amount3` decimal(10,2) DEFAULT NULL,
  `mc_amount1` decimal(10,2) DEFAULT NULL,
  `mc_amount2` decimal(10,2) DEFAULT NULL,
  `mc_amount3` decimal(10,2) DEFAULT NULL,
  `recurring` int(1) DEFAULT '0',
  `reattempt` int(1) DEFAULT '0',
  `retry_at` date DEFAULT NULL,
  `recur_times` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `subscr_id` varchar(20) DEFAULT NULL,
  `case_id` varchar(100) DEFAULT NULL,
  `case_type` varchar(30) DEFAULT NULL,
  `case_creation_date` datetime DEFAULT NULL,
  `receipt_ID` varchar(100) DEFAULT NULL,
  `item_name1` varchar(200) DEFAULT NULL,
  `item_number1` varchar(50) DEFAULT NULL,
  `quantity1` int(11) DEFAULT NULL,
  `mc_gross1` decimal(10,2) DEFAULT NULL,
  `mc_handling` decimal(10,2) DEFAULT NULL,
  `mc_handling1` decimal(10,2) DEFAULT NULL,
  `mc_shipping` decimal(10,2) DEFAULT NULL,
  `mc_shipping1` decimal(10,2) DEFAULT NULL,
  `processed` int(1) DEFAULT '0',
  `transaction_subject` varchar(255) DEFAULT NULL,
  `mc_gross_1` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paypal_transactions`
--

LOCK TABLES `paypal_transactions` WRITE;
/*!40000 ALTER TABLE `paypal_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `paypal_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `playername` varchar(45) NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  `createdon` datetime NOT NULL,
  `channel` int(10) unsigned NOT NULL,
  `inactive` int(1) unsigned NOT NULL,
  `venuetype` int(10) unsigned NOT NULL,
  `dirty` int(1) unsigned NOT NULL DEFAULT '0',
  `lastuptime` datetime NOT NULL,
  `verify` varchar(100) NOT NULL,
  `lastip` varchar(40) NOT NULL,
  `disable` int(1) NOT NULL DEFAULT '0',
  `np` int(1) NOT NULL DEFAULT '1',
  `description` text,
  `playertype` int(11) NOT NULL DEFAULT '0',
  `livevideo` int(11) NOT NULL DEFAULT '0',
  `pro` int(1) NOT NULL DEFAULT '0',
  `prodelete` int(1) NOT NULL DEFAULT '0',
  `lastadset` text,
  `lastadtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player`
--

LOCK TABLES `player` WRITE;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;
/*!40000 ALTER TABLE `player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playertypes`
--

DROP TABLE IF EXISTS `playertypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playertypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ptypeen` varchar(50) NOT NULL,
  `ptypees` varchar(50) NOT NULL,
  `ptypept` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playertypes`
--

LOCK TABLES `playertypes` WRITE;
/*!40000 ALTER TABLE `playertypes` DISABLE KEYS */;
INSERT INTO `playertypes` VALUES (1,'Single Player','Sencillo','Simple'),(2,'Dual Player','Doble','Dobro'),(3,'Workstation','En Estación','No Computador');
/*!40000 ALTER TABLE `playertypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playlist`
--

DROP TABLE IF EXISTS `playlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `playlistname` varchar(45) NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  `lastmodified` datetime NOT NULL,
  `verifier` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playlist`
--

LOCK TABLES `playlist` WRITE;
/*!40000 ALTER TABLE `playlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `playlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playlistitem`
--

DROP TABLE IF EXISTS `playlistitem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playlistitem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mediaid` int(10) unsigned NOT NULL DEFAULT '0',
  `order` int(10) unsigned NOT NULL,
  `playlistid` int(10) unsigned NOT NULL,
  `duration` int(10) unsigned NOT NULL,
  `npi` int(1) NOT NULL DEFAULT '1',
  `deleteflag` int(1) unsigned zerofill NOT NULL DEFAULT '0',
  `nomediatype` int(11) NOT NULL DEFAULT '0',
  `url` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playlistitem`
--

LOCK TABLES `playlistitem` WRITE;
/*!40000 ALTER TABLE `playlistitem` DISABLE KEYS */;
/*!40000 ALTER TABLE `playlistitem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qsubmit`
--

DROP TABLE IF EXISTS `qsubmit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qsubmit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qsubmit`
--

LOCK TABLES `qsubmit` WRITE;
/*!40000 ALTER TABLE `qsubmit` DISABLE KEYS */;
INSERT INTO `qsubmit` VALUES (2,'KSSLDKDKFLFLLDF?');
/*!40000 ALTER TABLE `qsubmit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regionplaylists`
--

DROP TABLE IF EXISTS `regionplaylists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `regionplaylists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `regionid` int(11) NOT NULL,
  `playlist` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regionplaylists`
--

LOCK TABLES `regionplaylists` WRITE;
/*!40000 ALTER TABLE `regionplaylists` DISABLE KEYS */;
/*!40000 ALTER TABLE `regionplaylists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regiontype`
--

DROP TABLE IF EXISTS `regiontype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `regiontype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `regiontype` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regiontype`
--

LOCK TABLES `regiontype` WRITE;
/*!40000 ALTER TABLE `regiontype` DISABLE KEYS */;
INSERT INTO `regiontype` VALUES (1,'Main Media Region'),(2,'RSS Region'),(3,'Side Bar');
/*!40000 ALTER TABLE `regiontype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rss_sources`
--

DROP TABLE IF EXISTS `rss_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rss_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `URL` varchar(255) NOT NULL DEFAULT '',
  `logo_dark` varchar(1024) NOT NULL DEFAULT 'http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf',
  `logo_light` varchar(1024) NOT NULL DEFAULT 'http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rss_sources`
--

LOCK TABLES `rss_sources` WRITE;
/*!40000 ALTER TABLE `rss_sources` DISABLE KEYS */;
INSERT INTO `rss_sources` VALUES (1,'El Nuevo Día','http://www.elnuevodia.com/rss/noticias.xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/elnuevodia-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/elnuevodia-w.swf'),(6,'Wall Street Journal','http://feeds.wsjonline.com/wsj/xml/rss/3_7011.xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/wsj-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/wsj-w.swf'),(3,'CNN','http://rss.cnn.com/rss/cnn_topstories.rss','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/cnn-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/cnn-w.swf'),(4,'BBC','http://newsrss.bbc.co.uk/rss/newsonline_world_edition/front_page/rss.xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/bbcnews-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/bbcnews-w.swf'),(5,'NY Times','http://www.nytimes.com/services/xml/rss/nyt/HomePage.xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/nyt-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/nyt-b.swf'),(7,'Gizmodo','http://gizmodo.com/tag/top/index.xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(8,'TechCrunch','http://feeds.feedburner.com/TechCrunch','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(9,'Engadget','http://feeds.engadget.com/weblogsinc/engadget','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/engadget-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/engadget-w.swf'),(10,'Digg','http://digg.com/rss/index.xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/digg-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/digg-w.swf'),(11,'Google News','http://news.google.com/?output=rss','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/google-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/google-w.swf'),(12,'Washington Post','http://feeds.washingtonpost.com/wp-dyn/rss/business/index_xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/twp-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/twp-w.swf'),(13,'Dictionary.com Word of The Day','http://dictionary.reference.com/wordoftheday/wotd.rss','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(14,'Folha Online - Cotidiano ','http://feeds.folha.uol.com.br/folha/cotidiano/rss091.xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(15,'Gazzetta.it-Homepage','http://www.gazzetta.it/rss/Home.xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(16,'El País - España','http://www.elpais.com/rss.html','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(17,'Shell Security (Es)','http://www.shellsec.net/index.rdf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(18,'Periodista Digital','http://www.periodistadigital.com/periodistadigital.rss','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(19,'Lalibre.be - La Une','http://www.lalibre.be/rss','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(20,'Liberation.fr - A la une','http://www.liberation.fr/rss.php','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(21,'Tagesschau im Internet','http://www.tagesschau.de/xml/tagesschau-meldungen/','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(22,'Stern.de Newsfeed -Computer & Technik','http://www.stern.de/standard/rss.php?channel=computer-technik','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(23,'Info Abril (Brasil) ','http://info.abril.com.br/aberto/infonews/rssnews.xml','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf'),(24,'El Tiempo (Colombia)','http://feeds.eltiempo.com/eltiempo/titulares','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-b.swf','http://media1.s3mer.com.s3.amazonaws.com/app/rss-logos/rss-w.swf');
/*!40000 ALTER TABLE `rss_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `show`
--

DROP TABLE IF EXISTS `show`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `showname` varchar(45) NOT NULL,
  `createdon` datetime NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  `shared` int(10) unsigned NOT NULL,
  `layouttype` int(10) unsigned NOT NULL,
  `resx` int(10) unsigned NOT NULL,
  `resy` int(10) unsigned NOT NULL,
  `description` varchar(200) NOT NULL DEFAULT '',
  `disable` int(1) NOT NULL DEFAULT '0',
  `ns` int(1) NOT NULL DEFAULT '1',
  `template` int(11) NOT NULL DEFAULT '0',
  `showtype` int(11) NOT NULL DEFAULT '0',
  `backgroundimage` int(11) NOT NULL DEFAULT '0',
  `clock` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `show`
--

LOCK TABLES `show` WRITE;
/*!40000 ALTER TABLE `show` DISABLE KEYS */;
/*!40000 ALTER TABLE `show` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `showregion`
--

DROP TABLE IF EXISTS `showregion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `showregion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `showid` int(10) unsigned DEFAULT NULL,
  `top` int(10) unsigned DEFAULT NULL,
  `left` int(10) unsigned DEFAULT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `fontcolor` varchar(45) DEFAULT NULL,
  `bgcolor1` varchar(45) DEFAULT NULL,
  `bgcolor2` varchar(45) DEFAULT NULL,
  `fontsize` int(10) unsigned DEFAULT NULL,
  `borderwidth` int(10) unsigned DEFAULT NULL,
  `bordercolor` varchar(45) DEFAULT NULL,
  `fontface` varchar(45) DEFAULT NULL,
  `opacity` int(10) unsigned DEFAULT NULL,
  `zindex` int(10) unsigned DEFAULT NULL,
  `backgroundimage` varchar(200) DEFAULT '',
  `template` int(11) DEFAULT '0',
  `templateregion` int(11) DEFAULT '0',
  `verifier` int(11) DEFAULT NULL,
  `rssid` int(11) NOT NULL DEFAULT '0',
  `rssurl` varchar(200) NOT NULL DEFAULT '',
  `mainmedia` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `showregion`
--

LOCK TABLES `showregion` WRITE;
/*!40000 ALTER TABLE `showregion` DISABLE KEYS */;
/*!40000 ALTER TABLE `showregion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timeformats`
--

DROP TABLE IF EXISTS `timeformats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timeformats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `format` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timeformats`
--

LOCK TABLES `timeformats` WRITE;
/*!40000 ALTER TABLE `timeformats` DISABLE KEYS */;
INSERT INTO `timeformats` VALUES (1,'12H'),(2,'24H');
/*!40000 ALTER TABLE `timeformats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `firstname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `joinedon` datetime NOT NULL,
  `accounttype` int(10) unsigned NOT NULL,
  `address1` text NOT NULL,
  `address2` text NOT NULL,
  `city` text NOT NULL,
  `stateprovince` text NOT NULL,
  `postalcode` text NOT NULL,
  `country` int(11) NOT NULL,
  `businessname` text NOT NULL,
  `venue` int(11) NOT NULL DEFAULT '0',
  `token` varchar(150) NOT NULL DEFAULT '0',
  `othervenue` varchar(50) DEFAULT NULL,
  `newsletter` int(1) NOT NULL DEFAULT '0',
  `quota` int(11) NOT NULL DEFAULT '0',
  `dateformat` int(11) NOT NULL DEFAULT '0',
  `timeformat` int(11) NOT NULL DEFAULT '0',
  `tutorial` int(11) NOT NULL DEFAULT '0',
  `pass_reset_token` tinytext NOT NULL,
  `prosubscriptions` int(11) NOT NULL DEFAULT '0',
  `subscriptionid` varchar(100) DEFAULT NULL,
  `insider` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venuetypes`
--

DROP TABLE IF EXISTS `venuetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `venuetypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `venueen` varchar(45) NOT NULL,
  `venuees` varchar(45) NOT NULL,
  `venuept` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venuetypes`
--

LOCK TABLES `venuetypes` WRITE;
/*!40000 ALTER TABLE `venuetypes` DISABLE KEYS */;
INSERT INTO `venuetypes` VALUES (1,'Hospitality','Hospitalidad','Restaurantes'),(2,'Medicine','Medicina','Medicina'),(3,'Retail','Detallista','Vendas Gerais'),(4,'Food','Comida','Comida'),(5,'Tranportation','Transportación','Tranportation');
/*!40000 ALTER TABLE `venuetypes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-08-10 19:43:36
