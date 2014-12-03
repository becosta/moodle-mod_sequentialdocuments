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
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function toggle_visibility(id) {
    var e = document.getElementById(id);
    //var label = document.getElementById('x' + id);

    if(e.style.display == 'none') {
      //label.src = label.src.replace('collapsed', 'expanded');
      e.style.display = 'block';
      col.innerHTML = valu;
    }
    else {
        //label.src = label.src.replace('expanded', 'collapsed');
        e.style.display = 'none';
    }
}

function force_visibility(id) {
    var e = document.getElementById(id);

    alert(e);
    if (e.style.display === null || e.style.display === 'none') {
        toggle_visibility(id);
    }
}

function toggle_postload_visibility(classname) {
    var elements = document.getElementsByClassName(classname);
    for (var i = 0; i < elements.length; i++) {
        var element = elements.item(i);
        if (!element.hasAttribute('data-sqds-is-last')) {
            if (!element.hasAttribute('id')) {
                $version.id = 'sqds-element-content-js-' + i;
            }
            toggle_visibility(element.id);
        }
    }
}

function sqds_postload() {
    toggle_postload_visibility('sqds-version-content');
    toggle_postload_visibility('sqds-document-content');
}





