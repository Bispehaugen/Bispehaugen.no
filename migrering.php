<?php

$stmt = $dbh->query("SELECT MAX(id) FROM `migreringer`");
$forrige_migrering = $stmt->fetchColumn();

function migrering($id) {
    global $forrige_migrering;
    global $dbh;

    $arg_list = func_get_args();
    // sjekk om $nr er kjørt
    if ($forrige_migrering >= $id) return;

    // Kjør alle strenger som blir sendt inn
    for($i = 2; $i<func_num_args(); $i++) {
        try {
            $dbh->query($arg_list[$i]);
        } catch (PDOException $e) {
            die('Migrering id: '.$id.'. Query number '.$i.'. Invalid query: ' . $e->getMessage());
        }
    }

    // Sett inn at migrering er kjørt
    $dbh->query("INSERT INTO `migreringer` (`id`, `kommentar`) VALUES ('".$id."', '".$arg_list[1]."')");
}

migrering(1, "opprett migreringer tabell",
"CREATE TABLE IF NOT EXISTS `migreringer` (
  `id` int(11) NOT NULL,
  `tid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `kommentar` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
);


migrering(3, "Diverse",
    "SELECT * FROM `medlemmer`"
/*

    "INSERT INTO `solfrih_bukdb`.`medlemmer` (`medlemsid`, `fnavn`, `enavn`, `adresse`, `postnr`, `tlfprivat`, `tlfmobil`, `tlfarbeid`, `instrument`, `verv`, `email`, `http`, `msn`, `beskrivelsesdok`, `poststed`, `status`, `instnr`, `grleder`, `brukernavn`, `passord`, `foto`, `bakgrunn`, `andreinstr`, `fdato`, `utdanning`, `studieyrke`, `bosted`, `kommerfra`, `ommegselv`, `startetibuk`, `sluttetibuk`, `sluttetibuk_date`, `startetibuk_date`, `avatar`, `rettigheter`, `begrenset`) VALUES ('5', 'Ola', 'Tveit', '', '', NULL, NULL, NULL, '', NULL, 'Slagverk', NULL, NULL, NULL, '', 'Sluttet', '0', '0', 'olatveit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '0'), ('4', 'Ronny', 'Lauten', '', '', NULL, NULL, NULL, '', NULL, 'Slagverk', NULL, NULL, NULL, '', 'Sluttet', '0', '0', 'olatveit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '0')",
    "UPDATE `solfrih_bukdb`.`forum_innlegg_ny` SET `skrevetavid` = '5' WHERE `forum_innlegg_ny`.`innleggid` =4832",
    "UPDATE  `solfrih_bukdb`.`forum_innlegg_ny` SET  `skrevetavid` =  '4' WHERE  `forum_innlegg_ny`.`skrevetavid` is NULL",
    "UPDATE  `solfrih_bukdb`.`forum_innlegg_ny` SET  `skrevetavid` =  '111' WHERE  `forum_innlegg_ny`.`skrevetavid` = 0",
    "UPDATE medlemmer, forum_innlegg_ny SET skrevetavid=medlemmer.medlemsid WHERE CONCAT(medlemmer.fnavn," ", medlemmer.enavn)=forum_innlegg_ny.skrevetav",

*/

);


migrering(4, "Noter og besetning",
"CREATE TABLE IF NOT EXISTS `noter_besetning` (
  `besetningsid` int(11) NOT NULL AUTO_INCREMENT,
  `besetningstype` varchar(255) NOT NULL,
  PRIMARY KEY (`besetningsid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10",
"INSERT INTO `noter_besetning` (`besetningsid`, `besetningstype`) VALUES
(1, 'janitsjar'),
(2, 'tyrolder'),
(3, 'brass'),
(4, 'ensamble'),
(5, 'storband'),
(6, 'kor'),
(7, 'juletrefest'),
(8, 'annet'),
(9, 'signal-signalmarsj') ON DUPLICATE KEY UPDATE besetningstype=besetningstype;"
);

migrering(5, "Noter konsert",
"CREATE TABLE IF NOT EXISTS `noter_konsert` (
  `key` int(11) NOT NULL AUTO_INCREMENT,
  `arrid` int(11) NOT NULL,
  `noteid` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=205 ;",
"INSERT INTO `noter_konsert` (`key`, `arrid`, `noteid`) VALUES
(6, 525, 210),
(7, 525, 252),
(8, 525, 295),
(9, 525, 310),
(10, 525, 363),
(14, 523, 216),
(15, 523, 271),
(16, 523, 234),
(17, 523, 340),
(18, 523, 361),
(19, 523, 377),
(20, 523, 406),
(21, 523, 216),
(22, 523, 271),
(23, 523, 234),
(24, 523, 340),
(25, 523, 361),
(26, 523, 377),
(27, 523, 406),
(28, 554, 269),
(29, 691, 293),
(30, 691, 331),
(31, 691, 376),
(32, 607, 211),
(33, 607, 212),
(34, 607, 238),
(35, 607, 242),
(36, 607, 255),
(37, 607, 264),
(38, 607, 276),
(39, 607, 291),
(40, 607, 308),
(41, 607, 332),
(42, 607, 334),
(43, 607, 346),
(44, 607, 403),
(45, 607, 348),
(46, 690, 312),
(47, 687, 240),
(48, 687, 257),
(49, 687, 373),
(50, 687, 274),
(51, 687, 341),
(52, 687, 342),
(53, 687, 385),
(54, 718, 290),
(55, 718, 298),
(56, 718, 321),
(57, 718, 381),
(58, 695, 220),
(59, 695, 300),
(60, 695, 304),
(61, 695, 326),
(62, 695, 327),
(63, 695, 328),
(64, 695, 344),
(65, 695, 356),
(66, 695, 357),
(67, 695, 369),
(68, 723, 223),
(69, 714, 226),
(70, 714, 266),
(71, 714, 268),
(72, 714, 286),
(73, 714, 305),
(74, 714, 338),
(75, 714, 400),
(76, 714, 235),
(77, 801, 311),
(78, 801, 390),
(79, 794, 324),
(80, 794, 218),
(81, 794, 280),
(82, 794, 382),
(83, 794, 383),
(84, 794, 351),
(85, 794, 359),
(86, 750, 313),
(87, 756, 213),
(88, 756, 243),
(89, 756, 277),
(90, 756, 278),
(91, 756, 316),
(92, 756, 317),
(93, 756, 354),
(94, 756, 374),
(95, 895, 225),
(96, 895, 245),
(97, 895, 272),
(98, 895, 273),
(99, 895, 384),
(100, 895, 297),
(101, 822, 338),
(102, 907, 241),
(103, 907, 336),
(104, 907, 337),
(105, 907, 347),
(106, 907, 355),
(107, 907, 396),
(108, 907, 404),
(109, 805, 325),
(110, 806, 282),
(111, 806, 230),
(112, 806, 288),
(113, 806, 378),
(114, 806, 318),
(115, 806, 343),
(116, 806, 352),
(118, 806, 282),
(119, 806, 230),
(120, 806, 288),
(121, 806, 378),
(122, 806, 318),
(123, 806, 343),
(124, 806, 352),
(125, 975, 209),
(126, 975, 259),
(127, 975, 320),
(128, 975, 301),
(129, 975, 225),
(130, 975, 245),
(131, 975, 320),
(132, 975, 259),
(133, 975, 320),
(134, 975, 225),
(135, 975, 245),
(136, 910, 297),
(137, 910, 331),
(138, 910, 336),
(139, 910, 355),
(140, 910, 373),
(141, 989, 212),
(142, 989, 229),
(143, 989, 254),
(144, 989, 258),
(145, 989, 289),
(146, 989, 227),
(147, 989, 362),
(148, 989, 271),
(149, 989, 330),
(150, 989, 400),
(151, 989, 345),
(152, 989, 368),
(153, 989, 294),
(154, 989, 370),
(155, 989, 371),
(156, 989, 380),
(157, 989, 387),
(158, 989, 250),
(159, 989, 315),
(160, 989, 374),
(161, 971, 330),
(162, 902, 331),
(163, 902, 336),
(164, 902, 347),
(165, 956, 265),
(166, 956, 284),
(167, 956, 299),
(168, 956, 365),
(169, 956, 367),
(170, 956, 407),
(171, 956, 271),
(172, 956, 404),
(173, 956, 315),
(174, 0, 0),
(175, 956, 265),
(176, 956, 284),
(177, 956, 299),
(178, 956, 365),
(179, 956, 367),
(180, 956, 407),
(181, 956, 271),
(182, 956, 404),
(183, 956, 315),
(184, 1011, 239),
(185, 1009, 258),
(186, 1009, 362),
(187, 1045, 261),
(188, 1045, 222),
(189, 1045, 228),
(190, 1045, 233),
(191, 1045, 275),
(192, 1045, 394),
(193, 1045, 401),
(194, 1045, 268),
(195, 0, 0),
(196, 0, 0),
(197, 1045, 261),
(198, 1045, 222),
(199, 1045, 228),
(200, 1045, 233),
(201, 1045, 275),
(202, 1045, 394),
(203, 1045, 401),
(204, 1045, 268) ON DUPLICATE KEY UPDATE noteid = noteid"
);

migrering(6, "Noter notesett",
"CREATE TABLE IF NOT EXISTS `noter_notesett` (
  `noteid` int(11) NOT NULL AUTO_INCREMENT,
  `tittel` varchar(255) NOT NULL,
  `komponist` varchar(255) DEFAULT NULL,
  `filpath` text,
  `besetningsid` int(11) DEFAULT NULL,
  `arkivnr` int(11) DEFAULT NULL,
  PRIMARY KEY (`noteid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=409",
"INSERT INTO `noter_notesett` (`noteid`, `tittel`, `komponist`, `filpath`, `besetningsid`, `arkivnr`) VALUES
(207, '1-mai-sanger', NULL, '/noter/1-mai-sanger/', 1, NULL),
(208, '17-mai-sanger', NULL, '/noter/17-mai-sanger/', 1, NULL),
(209, 'a-christmas-carol', NULL, '/noter/a-christmas-carol/', 1, NULL),
(210, 'air', NULL, '/noter/air/', 1, NULL),
(211, 'all-i-ask-of-you', NULL, '/noter/all-i-ask-of-you/', 1, NULL),
(212, 'also-sprach-zarathuztra', NULL, '/noter/also-sprach-zarathuztra/', 1, NULL),
(213, 'armenian-dances', NULL, '/noter/armenian-dances/', 1, NULL),
(214, 'baby', NULL, '/noter/baby/', 1, NULL),
(215, 'bear-mountain', NULL, '/noter/bear-mountain/', 1, NULL),
(216, 'belkis-regina-da-saba', NULL, '/noter/belkis-regina-da-saba/', 1, NULL),
(217, 'bjelleklang', NULL, '/noter/bjelleklang/', 7, NULL),
(218, 'blowing', NULL, '/noter/blowing/', 1, NULL),
(219, 'blsefest', NULL, '/noter/blsefest/', 7, NULL),
(220, 'blue-shades', NULL, '/noter/blue-shades/', 1, NULL),
(221, 'brekkhusmarsjen-signal', NULL, '/noter/brekkhusmarsjen-signal/', 9, NULL),
(222, 'buglers-holiday', NULL, '/noter/buglers-holiday/', 1, NULL),
(223, 'bums-rush', NULL, '/noter/bums-rush/', 1, NULL),
(224, 'cantina-band', NULL, '/noter/cantina-band/', 4, NULL),
(225, 'cantique-de-noel', NULL, '/noter/cantique-de-noel/', 1, NULL),
(226, 'cartoon', NULL, '/noter/cartoon/', 1, NULL),
(227, 'chakra', NULL, '/noter/chakra/', 1, NULL),
(228, 'champagner-galopp', NULL, '/noter/champagner-galopp/', 1, NULL),
(229, 'chicken-run-main-title', NULL, '/noter/chicken-run-main-title/', 1, NULL),
(230, 'children-of-sanchez', NULL, '/noter/children-of-sanchez/', 1, NULL),
(231, 'childrens-overture', NULL, '/noter/childrens-overture/', 1, NULL),
(232, 'chorales', NULL, '/noter/chorales/', 1, NULL),
(233, 'clarinet-escapade', NULL, '/noter/clarinet-escapade/', 1, NULL),
(234, 'concerto-for-horn-no-1', NULL, '/noter/concerto-for-horn-no-1/', 1, NULL),
(235, 'concerto-in-c-major', NULL, '/noter/concerto-in-c-major/', 1, NULL),
(236, 'congratulations', NULL, '/noter/congratulations/', 1, NULL),
(237, 'country-band-march', NULL, '/noter/country-band-march/', 1, NULL),
(238, 'dag-efter-dag', NULL, '/noter/dag-efter-dag/', 1, NULL),
(239, 'dance-movements', NULL, '/noter/dance-movements/', 1, NULL),
(240, 'dance-of-the-hours', NULL, '/noter/dance-of-the-hours/', 1, NULL),
(241, 'danse-arabe', NULL, '/noter/danse-arabe/', 1, NULL),
(242, 'danse-mot-vr', NULL, '/noter/danse-mot-vr/', 1, NULL),
(243, 'danza-de-los-duendes', NULL, '/noter/danza-de-los-duendes/', 1, NULL),
(244, 'deilig-er-jorden', NULL, '/noter/deilig-er-jorden/', 1, NULL),
(245, 'deilig-er-jorden-2', NULL, '/noter/deilig-er-jorden-2/', 1, NULL),
(246, 'den-gamle-soga', NULL, '/noter/den-gamle-soga/', 1, NULL),
(247, 'det-gr-et-festtog-gjennom-landet', NULL, '/noter/det-gr-et-festtog-gjennom-landet/', 1, NULL),
(248, 'det-gr-likar-no', NULL, '/noter/det-gr-likar-no/', 1, NULL),
(249, 'det-lyser-i-stille-grender', NULL, '/noter/det-lyser-i-stille-grender/', 1, NULL),
(250, 'diciplin', NULL, '/noter/diciplin/', 1, NULL),
(251, 'div-signalnoter', NULL, '/noter/div-signalnoter/', 9, NULL),
(252, 'drottningholmsmusiken', NULL, '/noter/drottningholmsmusiken/', 1, NULL),
(253, 'du-grnne-glitrende-tre', NULL, '/noter/du-grnne-glitrende-tre/', 1, NULL),
(254, 'du-mkke-komme-her', NULL, '/noter/du-mkke-komme-her/', 8, NULL),
(255, 'duett', NULL, '/noter/duett/', 1, NULL),
(256, 'early-hungarian-dances', NULL, '/noter/early-hungarian-dances/', 1, NULL),
(257, 'egmont', NULL, '/noter/egmont/', 1, NULL),
(258, 'el-camino-real', NULL, '/noter/el-camino-real/', 1, NULL),
(259, 'elsas-procession-to-the-cathedral', NULL, '/noter/elsas-procession-to-the-cathedral/', 1, NULL),
(260, 'en-solskinnsdag', NULL, '/noter/en-solskinnsdag/', 1, NULL),
(261, 'entry-march-of-the-boyars', NULL, '/noter/entry-march-of-the-boyars/', 1, NULL),
(262, 'etyder', NULL, '/noter/etyder/', 8, NULL),
(263, 'excelsior', NULL, '/noter/excelsior/', 1, NULL),
(264, 'fairytale', NULL, '/noter/fairytale/', 1, NULL),
(265, 'fanfare-for-the-common-man', NULL, '/noter/fanfare-for-the-common-man/', 3, NULL),
(266, 'fantasy', NULL, '/noter/fantasy/', 1, NULL),
(267, 'farvel-til-en-slavisk-kvinne', NULL, '/noter/farvel-til-en-slavisk-kvinne/', 1, NULL),
(268, 'festival-in-the-hall-of-the-mountain-king', NULL, '/noter/festival-in-the-hall-of-the-mountain-king/', 1, NULL),
(269, 'festival-overture', NULL, '/noter/festival-overture/', 1, NULL),
(270, 'festivalen-marsj', NULL, '/noter/festivalen-marsj/', 1, NULL),
(271, 'festive-overture', NULL, '/noter/festive-overture/', 1, NULL),
(272, 'festmusik-der-stadt-wien', NULL, '/noter/festmusik-der-stadt-wien/', 1, NULL),
(273, 'finale-symphony-no-3', NULL, '/noter/finale-symphony-no-3/', 1, NULL),
(274, 'finlandia', NULL, '/noter/finlandia/', 1, NULL),
(275, 'flklypa-medley', NULL, '/noter/flklypa-medley/', 1, NULL),
(276, 'fngad-av-en-stormvind', NULL, '/noter/fngad-av-en-stormvind/', 1, NULL),
(277, 'folk-dances', NULL, '/noter/folk-dances/', 1, NULL),
(278, 'four-scottish-dances', NULL, '/noter/four-scottish-dances/', 1, NULL),
(279, 'french-suite', NULL, '/noter/french-suite/', 1, NULL),
(280, 'gabriellas-sng', NULL, '/noter/gabriellas-sng/', 1, NULL),
(281, 'gammel-jegemarsj', NULL, '/noter/gammel-jegemarsj/', 1, NULL),
(282, 'gandalf', NULL, '/noter/gandalf/', 1, NULL),
(283, 'gud-signe-vrt-dyre-fedreland', NULL, '/noter/gud-signe-vrt-dyre-fedreland/', 1, NULL),
(284, 'happy-birthday-around-the-world', NULL, '/noter/happy-birthday-around-the-world/', 1, NULL),
(285, 'hei-h-n-er-det-jul-igjen', NULL, '/noter/hei-h-n-er-det-jul-igjen/', 7, NULL),
(286, 'hjalar-ljod', NULL, '/noter/hjalar-ljod/', 1, NULL),
(287, 'holmenkollmarsj', NULL, '/noter/holmenkollmarsj/', 1, NULL),
(288, 'hooray-for-hollywood', NULL, '/noter/hooray-for-hollywood/', 1, NULL),
(289, 'how-am-i-supposed-to-live-without-you', NULL, '/noter/how-am-i-supposed-to-live-without-you/', 1, NULL),
(290, 'hymn-to-the-fallen', NULL, '/noter/hymn-to-the-fallen/', 1, NULL),
(291, 'i-evighet', NULL, '/noter/i-evighet/', 1, NULL),
(292, 'in-memoriam', NULL, '/noter/in-memoriam/', 1, NULL),
(293, 'in-the-bleak-midwinter', NULL, '/noter/in-the-bleak-midwinter/', 1, NULL),
(294, 'in-the-mood', NULL, '/noter/in-the-mood/', 5, NULL),
(295, 'intermezzo', NULL, '/noter/intermezzo/', 1, NULL),
(296, 'internasjonalen', NULL, '/noter/internasjonalen/', 1, NULL),
(297, 'intrada', NULL, '/noter/intrada/', 1, NULL),
(298, 'intrada-ein-feste-burg', NULL, '/noter/intrada-ein-feste-burg/', 1, NULL),
(299, 'irish-tune-from-county-derry', NULL, '/noter/irish-tune-from-county-derry/', 1, NULL),
(300, 'irukandji', NULL, '/noter/irukandji/', 1, NULL),
(301, 'joy-to-the-world', NULL, '/noter/joy-to-the-world/', 1, NULL),
(302, 'julekveldsvise', NULL, '/noter/julekveldsvise/', 1, NULL),
(303, 'juletrefestnoter', NULL, '/noter/juletrefestnoter/', 7, NULL),
(304, 'just-do-it', NULL, '/noter/just-do-it/', 1, NULL),
(305, 'kobolt', NULL, '/noter/kobolt/', 1, NULL),
(306, 'kronprins-olavs-honnrmarsj', NULL, '/noter/kronprins-olavs-honnrmarsj/', 1, NULL),
(307, 'kungl-vaxholms-kustartilleriregementets-marsch', NULL, '/noter/kungl-vaxholms-kustartilleriregementets-marsch/', 1, NULL),
(308, 'la-det-swinge', NULL, '/noter/la-det-swinge/', 1, NULL),
(309, 'lang-fanfare', NULL, '/noter/lang-fanfare/', 9, NULL),
(310, 'largo-from-the-new-world', NULL, '/noter/largo-from-the-new-world/', 1, NULL),
(311, 'liberation', NULL, '/noter/liberation/', 1, NULL),
(312, 'listen-up-the-music-of-quincy-jones', NULL, '/noter/listen-up-the-music-of-quincy-jones/', 1, NULL),
(313, 'liturgical-dances', NULL, '/noter/liturgical-dances/', 1, NULL),
(314, 'liza', NULL, '/noter/liza/', 1, NULL),
(315, 'lvat', NULL, '/noter/lvat/', 1, NULL),
(316, 'lyrisk-dans', NULL, '/noter/lyrisk-dans/', 1, NULL),
(317, 'macarthur-park', NULL, '/noter/macarthur-park/', 1, NULL),
(318, 'married-life', NULL, '/noter/married-life/', 1, NULL),
(319, 'minss-marsjen', NULL, '/noter/minss-marsjen/', 1, NULL),
(320, 'mitt-hjerte-alltid-vanker', NULL, '/noter/mitt-hjerte-alltid-vanker/', 1, NULL),
(321, 'mountain-song', NULL, '/noter/mountain-song/', 1, NULL),
(322, 'musette', NULL, '/noter/musette/', 1, NULL),
(323, 'musevisa', NULL, '/noter/musevisa/', 1, NULL),
(324, 'music-from-apollo-13', NULL, '/noter/music-from-apollo-13/', 1, NULL),
(325, 'music-of-the-spheres', NULL, '/noter/music-of-the-spheres/', 1, NULL),
(326, 'musikk-for-den-nyankomne', NULL, '/noter/musikk-for-den-nyankomne/', 1, NULL),
(327, 'my-favorite-things', NULL, '/noter/my-favorite-things/', 1, NULL),
(328, 'my-ship', NULL, '/noter/my-ship/', 1, NULL),
(329, 'n-er-det-jul-igjen', NULL, '/noter/n-er-det-jul-igjen/', 7, NULL),
(330, 'new-life', NULL, '/noter/new-life/', 1, NULL),
(331, 'nimrod', NULL, '/noter/nimrod/', 1, NULL),
(332, 'nocturne', NULL, '/noter/nocturne/', 1, NULL),
(333, 'norge-i-rdt-hvitt-og-bltt', NULL, '/noter/norge-i-rdt-hvitt-og-bltt/', 1, NULL),
(334, 'oj-oj-oj-s-glad-jeg-skal-bli', NULL, '/noter/oj-oj-oj-s-glad-jeg-skal-bli/', 1, NULL),
(335, 'oppvarmingsvelser', NULL, '/noter/oppvarmingsvelser/', 8, NULL),
(336, 'orient-et-occident', NULL, '/noter/orient-et-occident/', 1, NULL),
(337, 'orient-express', NULL, '/noter/orient-express/', 1, NULL),
(338, 'overture-to-candide', NULL, '/noter/overture-to-candide/', 1, NULL),
(339, 'p-lven-sitter-nissen', NULL, '/noter/p-lven-sitter-nissen/', 7, NULL),
(340, 'peer-du-lyver-ja-vel-kaptein', NULL, '/noter/peer-du-lyver-ja-vel-kaptein/', 1, NULL),
(341, 'peer-gynt', NULL, '/noter/peer-gynt/', 1, NULL),
(342, 'pictures-at-an-exhibition', NULL, '/noter/pictures-at-an-exhibition/', 1, NULL),
(343, 'pirates-of-the-caribbean-at-worlds-end', NULL, '/noter/pirates-of-the-caribbean-at-worlds-end/', 1, NULL),
(344, 'point-blank', NULL, '/noter/point-blank/', 1, NULL),
(345, 'pomp-and-circumstance', NULL, '/noter/pomp-and-circumstance/', 1, NULL),
(346, 'prelude-to-the-te-deum', NULL, '/noter/prelude-to-the-te-deum/', 1, NULL),
(347, 'prince-of-persia', NULL, '/noter/prince-of-persia/', 1, NULL),
(348, 'river-dance', NULL, '/noter/river-dance/', 1, NULL),
(349, 'rosenborgsangen', NULL, '/noter/rosenborgsangen/', 1, NULL),
(350, 'rudolf-er-rd-p-nesen', NULL, '/noter/rudolf-er-rd-p-nesen/', 1, NULL),
(351, 'rusalkas-song-to-the-moon', NULL, '/noter/rusalkas-song-to-the-moon/', 1, NULL),
(352, 'rverkor-ronja-rverdatter', NULL, '/noter/rverkor-ronja-rverdatter/', 6, NULL),
(353, 's-gr-vi-rundt', NULL, '/noter/s-gr-vi-rundt/', 7, NULL),
(354, 'sabre-dance', NULL, '/noter/sabre-dance/', 1, NULL),
(355, 'scheherazade', NULL, '/noter/scheherazade/', 1, NULL),
(356, 'secret-concert', NULL, '/noter/secret-concert/', 1, NULL),
(357, 'serenety-and-disturbance', NULL, '/noter/serenety-and-disturbance/', 1, NULL),
(358, 'short-ride-in-a-fast-machine', NULL, '/noter/short-ride-in-a-fast-machine/', 1, NULL),
(359, 'sidus', NULL, '/noter/sidus/', 1, NULL),
(360, 'sketches-of-pain', NULL, '/noter/sketches-of-pain/', 1, NULL),
(361, 'slavonic-dances', NULL, '/noter/slavonic-dances/', 1, NULL),
(362, 'slavonic-rhapsody-no-2', NULL, '/noter/slavonic-rhapsody-no-2/', 1, NULL),
(363, 'sleigh-ride', NULL, '/noter/sleigh-ride/', 1, NULL),
(364, 'snmannen-kalle', NULL, '/noter/snmannen-kalle/', 1, NULL),
(365, 'souvenir-de-cirque-renz', NULL, '/noter/souvenir-de-cirque-renz/', 1, NULL),
(366, 'st-louis-blues', NULL, '/noter/st-louis-blues/', 1, NULL),
(367, 'standards-in-medley', NULL, '/noter/standards-in-medley/', 1, NULL),
(368, 'star-wars-saga', NULL, '/noter/star-wars-saga/', 1, NULL),
(369, 'stara-planina', NULL, '/noter/stara-planina/', 1, NULL),
(370, 'summon-the-dragon', NULL, '/noter/summon-the-dragon/', 1, NULL),
(371, 'superman-march', NULL, '/noter/superman-march/', 1, NULL),
(372, 'symphonic-movement', NULL, '/noter/symphonic-movement/', 1, NULL),
(373, 'symphony-no-5-finale', NULL, '/noter/symphony-no-5-finale/', 1, NULL),
(374, 'tails-of-the-unexpected', NULL, '/noter/tails-of-the-unexpected/', 1, NULL),
(375, 'the-blues-brothers-revue', NULL, '/noter/the-blues-brothers-revue/', 1, NULL),
(376, 'the-earl-of-oxfords-march', NULL, '/noter/the-earl-of-oxfords-march/', 1, NULL),
(377, 'the-incredibles', NULL, '/noter/the-incredibles/', 1, NULL),
(378, 'the-james-bond-theme', NULL, '/noter/the-james-bond-theme/', 1, NULL),
(379, 'the-liberty-bell', NULL, '/noter/the-liberty-bell/', 1, NULL),
(380, 'the-muppet-show-theme', NULL, '/noter/the-muppet-show-theme/', 1, NULL),
(381, 'the-pines-of-the-appian-way', NULL, '/noter/the-pines-of-the-appian-way/', 1, NULL),
(382, 'the-planets-jupiter', NULL, '/noter/the-planets-jupiter/', 1, NULL),
(383, 'the-planets-mars', NULL, '/noter/the-planets-mars/', 1, NULL),
(384, 'the-polar-express', NULL, '/noter/the-polar-express/', 1, NULL),
(385, 'the-thievish-magpie', NULL, '/noter/the-thievish-magpie/', 1, NULL),
(386, 'the-thin-red-line', NULL, '/noter/the-thin-red-line/', 1, NULL),
(387, 'the-time-of-my-life', NULL, '/noter/the-time-of-my-life/', 1, NULL),
(388, 'the-voice-of-the-guns', NULL, '/noter/the-voice-of-the-guns/', 1, NULL),
(389, 'the-william-tell-overture', NULL, '/noter/the-william-tell-overture/', 1, NULL),
(390, 'toccata-and-fugue-in-d-minor', NULL, '/noter/toccata-and-fugue-in-d-minor/', 1, NULL),
(391, 'trompetfanfare', NULL, '/noter/trompetfanfare/', 9, NULL),
(392, 'tyrolder', NULL, '/noter/tyrolder/', 2, NULL),
(393, 'ute-p-bryggekanten', NULL, '/noter/ute-p-bryggekanten/', 1, NULL),
(394, 'valdresmarsj', NULL, '/noter/valdresmarsj/', 1, NULL),
(395, 'variasjoner', NULL, '/noter/variasjoner/', 4, NULL),
(396, 'variations-of-a-korean-folk-song', NULL, '/noter/variations-of-a-korean-folk-song/', 1, NULL),
(397, 'vi-tenner-v?re-lykter', NULL, '/noter/vi-tenner-v?re-lykter/', 1, NULL),
(398, 'vip-fanfare', NULL, '/noter/vip-fanfare/', 9, NULL),
(399, 'vp-sjefens-signalmarsj', NULL, '/noter/vp-sjefens-signalmarsj/', 9, NULL),
(400, 'wait-of-the-world', NULL, '/noter/wait-of-the-world/', 1, NULL),
(401, 'walking-on-sunshine', NULL, '/noter/walking-on-sunshine/', 1, NULL),
(402, 'waterloo', NULL, '/noter/waterloo/', 1, NULL),
(403, 'waterloo-2', NULL, '/noter/waterloo-2/', 1, NULL),
(404, 'wedding-dance', NULL, '/noter/wedding-dance/', 1, NULL),
(405, 'winter-wonderland', NULL, '/noter/winter-wonderland/', 1, NULL),
(406, 'yiddish-dances', NULL, '/noter/yiddish-dances/', 1, NULL),
(407, 'you-know-my-name', NULL, '/noter/you-know-my-name/', 1, NULL),
(408, 'aa-s-svinger-vi', NULL, '/noter/aa-s-svinger-vi/', 1, NULL)  ON DUPLICATE KEY UPDATE arkivnr = arkivnr"
);



migrering(7, "Legg til start og slutt istede enn starttid og sluttid", 
    "UPDATE `arrangement` SET `start` = CONCAT( dato, \" \", starttid )",
    "UPDATE `arrangement` SET `slutt` = CONCAT( dato, \" \", sluttid )"
    );

migrering(8, "Drop diverse arrangement",
    "ALTER TABLE `arrangement` DROP `tildato`;",
    "ALTER TABLE `arrangement` DROP `starttid`;",
    "ALTER TABLE `arrangement` DROP `sluttid`;"
    );
    
migrering(9, "Legger til skrevetavid", 
    "ALTER TABLE  `nyheter` ADD  `skrevetavid` INT NOT NULL AFTER `skrevetav` ;",
    "UPDATE nyheter, medlemmer SET nyheter.skrevetavid = medlemmer.medlemsid WHERE nyheter.skrevetav = CONCAT(medlemmer.fnavn, CONCAT(\" \", medlemmer.enavn));",
    'UPDATE `nyheter` SET `skrevetavid` = 241 WHERE `nyhetsid` IN ("1227","1269","1270","1272","1273","1276","1281","1287","1291","1298","1299","1300","1305","1306","1308","1317","1332","1335","1340","1345","1347","1353","1354","1355","1362","1363","1372","1379","1390")',
    'UPDATE `nyheter` SET `skrevetavid` = 290 WHERE `nyhetsid` IN ("1265","1302","1303","1319","1356","1357","1385","1391")',
    'UPDATE `nyheter` SET `skrevetavid` = 312 WHERE `nyhetsid` IN ("1377")'
);

migrering(10, "Ukjente datoer blir satt til 2004", 
    "UPDATE forum_tema SET tidsisteinnlegg = startet WHERE `tidsisteinnlegg` IS NULL;",
    "UPDATE forum_tema SET tidsisteinnlegg = startet WHERE `tidsisteinnlegg` = \"0000-00-00 00:00:00\";"
);

migrering(11, "Pris 0 som standard er uheldig", 
    "ALTER TABLE  `nyheter` CHANGE  `normal_pris`  `normal_pris` VARCHAR( 5 ) NOT NULL",
    "ALTER TABLE  `nyheter` CHANGE  `student_pris`  `student_pris` VARCHAR( 5 ) NOT NULL"
);
    
migrering(12, "Standarden var 0 før, blank nå, kunne vært null, men jeg er lat",
    "UPDATE `nyheter` SET normal_pris = \"\" WHERE normal_pris = \"0\"",
    "UPDATE `nyheter` SET student_pris = \"\" WHERE student_pris = \"0\""
);

migrering(13, "Konsertpriser må kunne være null, da 0 gir empty() true -.-", 
    "ALTER TABLE  `nyheter` CHANGE  `normal_pris`  `normal_pris` VARCHAR( 5 ) NULL",
    "ALTER TABLE  `nyheter` CHANGE  `student_pris`  `student_pris` VARCHAR( 5 ) NULL",
    "UPDATE `nyheter` SET normal_pris = null WHERE normal_pris = \"\"",
    "UPDATE `nyheter` SET student_pris = null WHERE student_pris = \"\""
);

migrering(14, "Legger til rettigheter på forum", 
    "ALTER TABLE  `forum` ADD  `rettigheter` TINYINT NOT NULL DEFAULT  '0' AFTER  `pos`",
    "UPDATE `forum` SET  `rettigheter` =  '2' WHERE  `forumid`=3 AND `forumid` = 4"
);


migrering(15, "Legger til nye forum_innlegg_ny",
"INSERT INTO `forum_innlegg_ny` SELECT * FROM `forum_innlegg` ;",
"UPDATE `forum_innlegg_ny` SET `skrevetavid` = '5' WHERE `forum_innlegg_ny`.`innleggid` =4832",
"UPDATE `forum_innlegg_ny` SET  `skrevetavid` =  '4' WHERE  `forum_innlegg_ny`.`skrevetavid` is NULL",
"UPDATE `forum_innlegg_ny` SET  `skrevetavid` =  '111' WHERE  `forum_innlegg_ny`.`skrevetavid` = 0",
"UPDATE medlemmer, forum_innlegg_ny SET skrevetavid=medlemmer.medlemsid WHERE CONCAT(medlemmer.fnavn,\" \", medlemmer.enavn)=forum_innlegg_ny.skrevetav"
);


migrering(16, "Legger til glemt passord token på medlemmer",
"ALTER TABLE  `medlemmer` ADD  `bytt_passord_token` VARCHAR( 40 ) NULL DEFAULT NULL ;"
);

migrering(17, "Melding på weblog", 
"ALTER TABLE  `weblog` ADD  `melding` TEXT NULL");

migrering(18, "Fjerner begrensning på weblog id",
"ALTER TABLE  `weblog` CHANGE  `id`  `id` INT NOT NULL AUTO_INCREMENT");

migrering(19, "opprett tabell med kobling mellom arrid og nyhetsid (KUN FOR KONSERTER)",
"CREATE TABLE IF NOT EXISTS `konserter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `arrid_konsert` int(11) NOT NULL,
  `nyhetsid_konsert` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
);

migrering(20, "Legger til filer tabell for bilder, dokumenter og etterhvert noter", 
    "CREATE TABLE IF NOT EXISTS `filer` (
`id` int(11) NOT NULL,
  `filnavn` varchar(255) NOT NULL,
  `tittel` varchar(255) NOT NULL,
  `beskrivelse` text NOT NULL,
  `filtype` varchar(15) NOT NULL,
  `medlemsid` int(11) NOT NULL,
  `mappeid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
"ALTER TABLE `filer`
 ADD PRIMARY KEY (`id`);",
"ALTER TABLE `filer`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;"
);

migrering(21, "Legger til mapper tabell for bilder, dokumenter og etterhvert noter", 
    "CREATE TABLE IF NOT EXISTS `mapper` (
`id` int(11) NOT NULL,
  `mappenavn` varchar(255) NOT NULL,
  `tittel` varchar(255) NOT NULL,
  `beskrivelse` text NOT NULL,
  `mappetype` smallint(6) NOT NULL,
  `foreldreid` int(11) NOT NULL,
  `filid` int(11) DEFAULT NULL COMMENT 'FilId for bilde',
  `komiteid` int(11) DEFAULT NULL COMMENT 'Tilhørighet'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
"ALTER TABLE `mapper`
 ADD PRIMARY KEY (`id`);",
"ALTER TABLE `mapper`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;"
);

migrering(22, "Legger til mappetyper tabellen (f.eks. album, dokumenter, notemappe)", 
    "CREATE TABLE IF NOT EXISTS `mappetyper` (
`id` int(11) NOT NULL,
  `navn` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
"ALTER TABLE `mappetyper`
 ADD PRIMARY KEY (`id`);",
"ALTER TABLE `mappetyper`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;"
);

migrering(23, "Legger til mappetyper (f.eks. album, dokumenter, notemappe)", 
    "INSERT INTO `buk`.`mappetyper` (`id`, `navn`) VALUES (NULL, 'album'), (NULL, 'dokumenter');"
);

migrering(24, "Legger til idpath i mapper-tabellen", 
    "ALTER TABLE `mapper` ADD `idpath` VARCHAR(255) NOT NULL AFTER `mappenavn`;"
);

migrering(25, "Legger til idpath i filer-tabellen", 
    "ALTER TABLE `filer` ADD `idpath` VARCHAR(255) NOT NULL AFTER `filnavn`;"
);

migrering(26, "Legger til tid på filer", 
    "ALTER TABLE `filer` ADD `tid` TIMESTAMP NOT NULL AFTER `mappeid`;"
);

migrering(27, "Fjern idpath på filer", 
    "ALTER TABLE `filer` DROP `idpath`;"
);

migrering(28, "Legg til mappeid på notesett",
    "ALTER TABLE `noter_notesett` ADD `mappeid` INT NULL AFTER `filpath`;"
);

migrering(29, "Fjern idpath på mapper", 
    "ALTER TABLE `mapper` DROP `idpath`;"
);

migrering(30, "Legger til mappetype på filer, skal speile mappetype til mappeid, for å lette søk",
    "ALTER TABLE `filer` ADD `mappetype` SMALLINT(6) NOT NULL AFTER `mappeid`;",
    "UPDATE filer SET mappetype = 1"
);

migrering(31, "Arkivnr 0 er ubrukelig, ha null istede",
    "UPDATE noter_notesett SET arkivnr = NULL WHERE arkivnr = 0"
);

migrering(32, "Opprett varling tabell for å sjekke om epostvarsling er sendt ut",
"CREATE TABLE IF NOT EXISTS `varsling` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `arrid` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `tid` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"
);

migrering(33, "Legg til slagverkhjelp",
"CREATE TABLE IF NOT EXISTS `slagverkhjelp` (
  `medlemsid` int(11) NOT NULL,
  `gruppeid` int(11) NOT NULL
) DEFAULT CHARSET=latin1;
");

migrering(34, "Slagverkhjelp burde ha en id",
    "ALTER TABLE `slagverkhjelp` ADD PRIMARY KEY(`medlemsid`);"
);

migrering(35, "Slagverkhjelp skal kunne ha en leder",
    "ALTER TABLE `slagverkhjelp` ADD `gruppeleder` BOOLEAN NOT NULL DEFAULT FALSE ;"
);

migrering(36, "Weblog type trenger ikke å bare være 8 tegn...",
    "ALTER TABLE `weblog` CHANGE `type` `type` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;"
);

migrering(37, "Legger til hengerfeste på medlemmer",
    "ALTER TABLE `medlemmer` ADD `hengerfeste` BOOLEAN NOT NULL DEFAULT FALSE ;"
);

migrering(38, "Legger til slagverk gruppeid på arranngement",
    "ALTER TABLE `arrangement` ADD `slagverk` INT NOT NULL COMMENT 'gruppeid for slagverk' AFTER `hjelpere`;"
);

migrering(39, "Legger til bil på medlemmer",
    "ALTER TABLE `medlemmer` ADD `bil` BOOLEAN NOT NULL DEFAULT FALSE ;"
);

migrering(40, "Legg til medlemsid for varsling",
    "ALTER TABLE `varsling` ADD `medlemsid` INT NOT NULL AFTER `type`;"
);

migrering(41, "Oppretter en tabell for lagring av 'husk meg' token",
    "CREATE TABLE IF NOT EXISTS `husk_meg` (
        `id` int NOT NULL AUTO_INCREMENT,
        `serie` char(12),
        `token` char(64),
        `medlemsid` int,
        `sist_brukt` datetime,
        PRIMARY KEY (`id`)
    )"
);

migrering(42, "Oppretter en tabell for innhold på siden",
    "CREATE TABLE IF NOT EXISTS `innhold` (
        `id` int NOT NULL AUTO_INCREMENT,
        `navn` varchar(50) NOT NULL UNIQUE,
        `tekst` text,
        PRIMARY KEY(`id`)
    )"
);

migrering(43, "Oppretter en tabell for bilder tilhørende innhold på siden",
    "CREATE TABLE IF NOT EXISTS `innhold_bilder` (
        `id` int NOT NULL AUTO_INCREMENT,
        `type` varchar(5) NOT NULL,
        `innhold_id` int NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`innhold_id`) REFERENCES `innhold` (`id`)
    )"
);

migrering(44, "Oppretter en mange-til-mange tabell for kakebakere",
    "CREATE TABLE IF NOT EXISTS `kakebakere` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `medlemsid` INT NOT NULL,
        `arrid` INT NOT NULL
    )"
);

migrering(46, "Importerer kakebakere til mange-til-mange tabellen",
    "INSERT INTO `kakebakere` (`medlemsid`, `arrid`)
        SELECT `kakebaker`, `arrid`
        FROM `arrangement` AS arr JOIN `medlemmer` AS m
        ON arr.kakebaker = m.medlemsid"
);

    /*
migrering(17, "Neste kommer her", 
    "INSERT INTO ..."
);
*/
