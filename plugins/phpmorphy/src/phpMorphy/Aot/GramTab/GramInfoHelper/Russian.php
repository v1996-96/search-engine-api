<?php
/*
* This file is part of phpMorphy project
*
* Copyright (c) 2007-2012 Kamaev Vladimir <heromantor@users.sourceforge.net>
*
*     This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2 of the License, or (at your option) any later version.
*
*     This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
*     You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the
* Free Software Foundation, Inc., 59 Temple Place - Suite 330,
* Boston, MA 02111-1307, USA.
*/

class phpMorphy_Aot_GramTab_GramInfoHelper_Russian
    extends phpMorphy_Aot_GramTab_GramInfoHelperAbstract
{
    /**
     * @param string $partOfSpeech
     * @param string[] $grammems
     * @return string
     */
    function convertPartOfSpeech($partOfSpeech, $grammems) {
        if($partOfSpeech == 'Г') {
            if(in_array('прч', $grammems)) {
                return 'ПРИЧАСТИЕ';
            } elseif(in_array('дпр', $grammems)) {
                return 'ДЕЕПРИЧАСТИЕ';
            } elseif(in_array('инф', $grammems)) {
                return 'ИНФИНИТИВ';
            }
        }

        return $partOfSpeech;
    }

    /**
     * @param string $partOfSpeech
     * @return bool
     */
    function isPartOfSpeechProductive($partOfSpeech) {
        static $map = array(
            "С" => 1,
            "ИНФИНИТИВ" => 1,
            "П" => 1,
            "Н" => 1,
        );
        
        return isset($map[$partOfSpeech]);
    }

}