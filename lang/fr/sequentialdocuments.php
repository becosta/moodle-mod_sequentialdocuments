<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * French strings for sequentialdocuments
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Documents Séquentiels';
$string['modulenameplural'] = 'Documents Séquentiels';
$string['pluginname'] = 'Documents Séquentiels';

$string['sequentialdocuments'] = 'sequentialdocuments';
$string['pluginadministration'] = 'Documents Séquentiels - Administration';

$string['sequentialdocumentsname'] = 'Documents Séquentiels';
$string['sequentialdocumentssname_help'] = 'This is the content of the help tooltip associated with the sequentialdocumentsname field. Markdown syntax is supported.';

$string['modulename_help'] = '';

// Instance tabs.
$string['tabhistory'] = 'Historique';
$string['tabindex'] = 'Documents';
$string['tabnewdocument'] = 'Créer un nouveau document';
$string['tabaccessrights'] = 'Modiffier les droits d\'accès';

// Document objects html.
$string['documenttitle'] = 'Titre:';
$string['documentdescription'] = 'Description:';
$string['documentcreated'] = 'Créé le:';
$string['documentmodified'] = 'Dernière modification:';
$string['documentcontent'] = 'Contenu:';
$string['documentaddversionlink'] = 'Ajouter une nouvelle version';
$string['documenteditlink'] = 'Editer';
$string['documentdeletelink'] = 'Supprimer';
$string['documentlocklink'] = 'Vérouiller ce document';
$string['documentunlocklink'] = 'Dévérouiller ce document';

// Version objects html.
$string['versionlink'] = 'Version: ';
$string['versionsubmitted'] = 'Envoyée le: ';
$string['versionduedate'] = 'Date limite de rendu:';
$string['versioneditlink'] = $string['documenteditlink'];
$string['versionaddfilelink'] = 'Attacher un fichier';
$string['versiondeletelink'] = $string['documentdeletelink'];
$string['versionpostfeedbacklink'] = 'Poster un feedback';
$string['versionlocklink'] = 'Vérouiller cette version';
$string['versionunlocklink'] = 'Dévérouiller cette version';

// Feedback
$string['feedbackattachments'] = 'Pièces jointes: ';
$string['feedbackeditlink'] = $string['documenteditlink'];
$string['feedbackdeletelink'] = $string['documentdeletelink'];
$string['feedbackcreateversionfromthis'] = 'Créer une version à partir de ce feedback';

// Feedback objects html.
$string['feedbacksubmitted'] = $string['versionsubmitted'];
$string['feedbackfrom'] = 'Feedback de: ';

// Interaction objects html.
$string['interacttitle'] = $string['documenttitle'];
$string['interactdescription'] = $string['documentdescription'];
$string['interactfrom'] = 'De: ';
$string['interacton'] = 'Le: ';
$string['interactnewdocument'] = 'Nouveau document ';
$string['interactnewversion'] = 'Nouvelle version ';
$string['interactnewfeedback'] = 'Nouveau feedback ';

// Versions due dates reminders.
$string['versionduesubject'] = 'Rappel de date limite de rendu de version';
$string['automaticmessage'] = 'ceci est un message automatique';
$string['versionduecorp'] = 'Une version de document est à rendre ';
$string['versionduelatesubject'] = 'Date limite de rendu de version dépassée';
$string['versionduelink'] = 'Voir cette version';
$string['versionduedueday'] = $string['versionduecorp'].'aujourd\'hui.<br />('.$string['automaticmessage'].')<br />';
$string['versiondueoneday'] = $string['versionduecorp'].'demain.<br />('.$string['automaticmessage'].')<br />';
$string['versiondueoneweek'] = $string['versionduecorp'].'dans une semaine.<br />('.$string['automaticmessage'].')<br />';
$string['versionduetwoweeks'] = $string['versionduecorp'].'dans deux semaines.<br />('.$string['automaticmessage'].')<br />';
$string['versiondueonemonth'] = $string['versionduecorp'].'dans un mois.<br />('.$string['automaticmessage'].')<br />';
$string['versionduelate'] = 'Une version de document est en retard.<br />('.$string['automaticmessage'].')';

// Forms
$string['requiredfield'] = 'Ce champ est obligatoire';
$string['files'] = 'Fichier(s)';

// mod_form.php
$string['mfteachers'] = 'Enseignants';
$string['mfstudents'] = 'Etudiants';

// add_document_form.php
$string['adfadddocument'] = 'Création de document';
$string['adftitle'] = 'Titre du document';
$string['adfdescription'] = 'Description du document';
$string['adfrequired'] = $string['requiredfield'];

// add_version_form.php
$string['avfaddversion'] = 'Création de version';
$string['avfduetime'] = 'Date limite de rendu: ';
$string['avfsendreminder'] = 'Envoyer un rappel ';
$string['avfsendreminderduedate'] = ' la date limite';
$string['avfdueday'] = $string['avfsendreminder'].'le jour même';
$string['avfoneday'] = $string['avfsendreminder'].'la veille de'.$string['avfsendreminderduedate'];
$string['avfoneweek'] = $string['avfsendreminder'].'une semaine avant'.$string['avfsendreminderduedate'];
$string['avftwoweeks'] = $string['avfsendreminder'].'deux semaines avant'.$string['avfsendreminderduedate'];
$string['avfonemonth'] = $string['avfsendreminder'].'un mois avant'.$string['avfsendreminderduedate'];
$string['avfpostneeded'] = $string['avfsendreminder'].'chaque semaine après'.$string['avfsendreminderduedate'].' pendant:';
$string['avfselectnever'] = 'Jamais';
$string['avfselectoneweek'] = '1 semaine';
$string['avfselecttwoweeks'] = '2 semaines';
$string['avfselectthreeweeks'] = '3 semaines';
$string['avfselectonemonth'] = '1 mois';
$string['avfselecttwomonth'] = '2 mois';
$string['avfselectthreemonth'] = '3 mois';
$string['avfselectsixmonth'] = '6 mois';
$string['avffiles'] = $string['files'];
$string['avfrequired'] = $string['requiredfield'];

// add_feedback_form.php
$string['affaddfeedback'] = 'Envoyer un feedback';
$string['affcontent'] = 'Contenu du feedback';
$string['afffiles'] = $string['files'];
$string['affrequired'] = $string['requiredfield'];


// access_rights_config_form.php
$string['arfdocumentaccess'] = 'Configuration des droits d\'accès des étudiants sur les documents';
$string['arfreaddocument'] = 'Droits de visualisation';
$string['arfcreatedocument'] = 'Droits de création';
$string['arfeditdocument'] = 'Droits d\'édition';
$string['arfdeletedocument'] = 'Droits de suppression';
$string['arfversionaccess'] = 'Configuration des droits d\'accès des étudiants sur les versions';
$string['arfreadversion'] = $string['arfreaddocument'];
$string['arfcreateversion'] = $string['arfcreatedocument'];
$string['arfeditversion'] = $string['arfeditdocument'];
$string['arfdeleteversion'] = $string['arfdeletedocument'];
$string['arffeedbackaccess'] = 'Configuration des droits d\'accès des étudiants sur les feedbacks';
$string['arfreadfeedback'] = $string['arfreaddocument'];
$string['arfcreatefeedback'] = $string['arfcreatedocument'];
$string['arfeditfeedback'] = $string['arfeditdocument'];
$string['arfdeletefeedback'] = $string['arfdeletedocument'];

// Messages.
$string['messageprovider:duedatereminder'] = 'Rappels de date limite de rendu de version';
$string['messageprovider:newsubmission'] = 'Vous notifie d\'un nouveau rendu d\'un étudiant';

// Cron tasks.
$string['sendreminders'] = 'Envoie des rappels de Documents Séquentiels';

// Errors.
$string['error'] = 'Erreur';
$string['pagenotfound'] = 'Page introuvable';
$string['missingcmorid'] = 'You must specify a course_module ID or an instance ID';
$string['missinginstanceaccess'] = 'Vous n\'avez pas les accès à cette instance de '.$string['modulename'];
$string['missinglockingrights'] = 'Vous n\'avez pas les droits de vérouillage';
$string['missingpageaccess'] = 'Vous n\'avez pas accès à cette page';
$string['invaliddocumentid'] = 'Document id specifié invalide';
$string['invalidversionid'] = 'Version id specifié invalide';
$string['invalidfeedbackid'] = 'Feedback id specifié invalide';
$string['missingdocumentaccess'] = 'Vous n\'avez pas accès à ce document';
$string['missingdocumentcreationrights'] = 'Vous n\'avez pas les droits de création de  document';
$string['missingdocumenteditionrights'] = 'Vous n\'avez pas les droits d\'édition de document';
$string['missingdocumentsuppressionrights'] = 'Vous n\'avez pas les droits de suppression de document';
$string['missingdocumentlockingrights'] = 'Vous n\'avez pas les droits pour vérouiller ce document';
$string['missingversionaccess'] = 'Vous n\'avez pas accès à cette version de document';
$string['missingversioncreationrights'] = 'Vous n\'avez pas les droits de création de version';
$string['missingversioneditionrights'] = 'Vous n\'avez pas les droits d\'édition de version';
$string['missingversionsuppressionrights'] = 'Vous n\'avez pas les droits de suppression de version';
$string['missingversionlockingrights'] = 'Vous n\'avez pas les droits pour vérouiller cette version';
$string['missingfeedbackaccess'] = 'Vous n\'avez pas accès à ce feedback';
$string['missingfeedbackcreationrights'] = 'Vous n\'avez pas les droits de création de feedback';
$string['missingfeedbackeditionrights'] = 'Vous n\'avez pas les droits d\'édition de feedback';
$string['missingfeedbacksuppressionrights'] = 'Vous n\'avez pas les droits de suppression de feedback';
$string['missingfeedbacklockingrights'] = 'Vous n\'avez pas les droits pour vérouiller ce feedback';


