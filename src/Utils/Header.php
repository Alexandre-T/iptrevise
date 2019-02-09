<?php
/*
 * This file is part of the LAPI Application.
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utils;
use App\Exception\LoadException;

/**
 * Outil pour détecter le positionnement de chacune des colonnes dans le fichier ucl.
 */
class Header
{
//    const C_TYPE = 0; //type
//    const C_SN = 1; //SN
//    const C_B = 2; //B
//    const C_J = 3; //J
//    const C_CREATED = 4; //T
//    const C_INCREMENT = 5; //A
//    const C_S = 6; //S
//    const C_PLAQUE_COURT = 7; //P
//    const C_PLAQUE_LONG = 8; //p
//    const C_R = 9; //R
//    const C_FIABILITE = 10; //f
//    const C_COORD = 11; //c
//    const C_H = 12; //H
//    const C_PAYS = 13; //N
//    const C_V = 14; //v
//    const C_IMAGE = 15; //F
//    const C_IMAGE_COULEUR = 16; //Fc
//    const C_FRLV1 = 17; //flrv1
//    const C_FRLV1C = 18; //flrv1c
//    const C_FRLV2 = 19; //flrv2
//    const C_FRLV2C = 20; //flrv2c
//    const C_N = 21; //n
//    const C_O = 22; //o
//    const C_O2 = 23; //O
//    const C_G = 24; //g
//    const C_NATURE_VEHICULE = 25; //l
//    const C_H2 = 26; //h
//    const C_I = 27; //i
//    const C_T = 28; //t
//    const C_D = 29; //D
//    const C_L2 = 30; //L
//    const C_S2 = 31; //s
//    const C_HEIGHT = 31; //height
//    const C_LSD = 32; //lsd
//    const C_SPEEDRNG = 33; //speedrng
//    const C_K = 34; //k
//    const C_LANE = 35; //lane
//    const C_SPEEDEST = 36; //speedest
//    const C_Q = 37; //q
//    const C_BGMEAN = 38; //bgmean
//    const C_FMEAN = 39; //fmean
//    const C_EXP = 40; //exp
//    const C_GAIN = 41; //gain
//    const C_CREJECT = 42; //creject
//    const C_RLVCREJ = 43; //rlvcrej
//    const C_STOPLINE = 44; //stopline
//    const C_RLVLOC = 45; //rlvLoc
//    const C_VC = 46; //Vc
//    const C_VRLVC = 47; //Vrlvc
//    const C_PLAQUE_COLLISION = 48; //Vrlvc
    
    /**
     * @var int
     */
    private $coord = -1;
    /**
     * @var int
     */
    private $created = -1;
    /**
     * @var int
     */
    private $fiability = -1;
    /**
     * @var int
     */
    private $h = -1;
    /**
     * @var int
     */
    private $image = -1;
    /**
     * @var int
     */
    private $plaqueCourte = -1;
    /**
     * @var int
     */
    private $plaqueCollision = -1;
    /**
     * @var int
     */
    private $plaqueLongue = -1;
    /**
     * @var int
     */
    private $increment = -1;
    /**
     * @var int
     */
    private $natureVehicule = -1;
    /**
     * @var int
     */
    private $r = -1;
    /**
     * @var int
     */
    private $s = -1;
    /**
     * @var int
     */
    private $pays = -1;

    /**
     * Construit la classe en fonction de l'entête fourni.
     * Lève une ereeur s'il manque une colonne
     *
     * Header constructor.
     * @param array $line1
     * @throws LoadException
     */
    public function __construct(array $line1)
    {
        foreach($line1 as $index => $name) {
            if ('c' == $name) {
                $this->coord = $index;
            } elseif ('T' == $name) {
                $this->created = $index;
            } elseif ('f' == $name) {
                $this->fiability = $index;
            } elseif ('h' == $name) {
                $this->h = $index;
            } elseif ('F' == $name) {
                $this->image = $index;
            } elseif ('P' == $name) {
                $this->plaqueCourte = $index;
            } elseif ('p' == $name) {
                $this->plaqueLongue = $index;
            } elseif ('A' == $name) {
                $this->increment = $index;
            } elseif ('l' == $name) {
                $this->natureVehicule = $index;
            } elseif ('R' == $name) {
                $this->r = $index;
            } elseif ('S' == $name) {
                $this->s = $index;
            } elseif ('N' == $name) {
                $this->pays = $index;
            }
        }

        $this->plaqueCollision = count($line1);

        if (-1 === $this->coord) {
            dump($this, $line1);
            throw new LoadException("Colonne coord non trouvée dans le fichier");
        }
        if (-1 === $this->created) {
            throw new LoadException("Colonne created non trouvée dans le fichier");
        }
        if (-1 === $this->fiability) {
            throw new LoadException("Colonne fiability non trouvée dans le fichier");
        }
        if (-1 === $this->h) {
            throw new LoadException("Colonne h non trouvée dans le fichier");
        }
        if (-1 === $this->image) {
            throw new LoadException("Colonne image non trouvée dans le fichier");
        }
        if (-1 === $this->plaqueCourte) {
            throw new LoadException("Colonne plaque_court non trouvée dans le fichier");
        }
        if (-1 === $this->plaqueLongue) {
            throw new LoadException("Colonne plaque_long non trouvée dans le fichier");
        }
        if (-1 === $this->increment) {
            throw new LoadException("Colonne increment non trouvée dans le fichier");
        }
        if (-1 === $this->r) {
            throw new LoadException("Colonne r non trouvée dans le fichier");
        }
        if (-1 === $this->s) {
            throw new LoadException("Colonne s non trouvée dans le fichier");
        }
        if (-1 === $this->pays) {
            throw new LoadException("Colonne pays non trouvée dans le fichier");
        }
    }

    /**
     * @param string $column
     * @return int
     * @throws LoadException
     */
    public function getColumn(string $column): int
    {
        switch ($column) {
            case 'coord':
                return $this->coord;
            case 'created':
                return $this->created;
            case 'fiability':
                return $this->fiability;
            case 'h':
                return $this->h;
            case 'image':
                return $this->image;
            case 'plaque_court':
                return $this->plaqueCourte;
            case 'plaque_long':
                return $this->plaqueLongue;
            case 'plaque_collision':
                return $this->plaqueCollision;
            case 'increment':
                return $this->increment;
            case 'nature_vehicule':
                return $this->natureVehicule;
            case 'r':
                return $this->r;
            case 's':
                return $this->s;
            case 'pays':
                return $this->pays;
            default:
                throw new LoadException(sprintf("Colonne %s inconnue", $column));
        }
    }
}
