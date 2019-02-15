<?php
/**
 * This file is part of the LAPI application.
 *
 * PHP version 7.2
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license   MIT
 *
 * @see https://github.com/Alexandre-T/casguard/blob/master/LICENSE
 */

namespace App\Utils;

use App\Exception\DownloadException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Outils pour faciliter le téléchargement.
 */
class DownloadUtils
{
    const REPERE = 'ucL1';

    /**
     * Ouvre un fichier distant, en récupère la liste de tous les fichiers disponibles.
     *
     * @param string $directory
     *
     * @return array
     *
     * @throws DownloadException
     */
    public function getFiles(string $directory): array
    {
        //lecture du fichier
        $html = @file_get_contents($directory);

        if (false === $html) {
            throw new DownloadException('Impossible de lire le contenu du répertoire d’archive de la caméra.');
        }

        $crawler = new Crawler($html);
        $links = $crawler->filterXPath('//a[contains(@href,"'.self::REPERE.'")]')->each(function (Crawler $node, $i) {
            return $node->text();
        });

        $files = [];
        foreach ($links as $link) {
            $files[$link] = $directory.$link;
        }

        return $files;
    }

    /**
     * Télécharge un fichier de passage et anonymise à la volée les plaques d'immatriculation.
     *
     * @param string $file Fichier à télécharger
     * @param string $code Code de la caméra
     * @param bool $override
     *
     * @return array
     *
     * @throws DownloadException
     */
    public function downloadFile(string $file, string $code, bool $override = false): array
    {
        $directory = __DIR__.'/../../data/downloaded/camera-'.$code;
        $outputFilename = basename($file).'.csv';
        $outputCompleteFilename = $directory.DIRECTORY_SEPARATOR.$outputFilename;

        if (!$override && is_file($outputCompleteFilename) && filesize($outputCompleteFilename) > 0) {
            throw new DownloadException('Le fichier '.$outputFilename.' a déjà été téléchargé et n’est pas vide. Je ne le télécharge pas une seconde fois.', DownloadException::WARNING);
        }

        if (!is_dir($directory) && false === @mkdir($directory)) {
            throw new DownloadException('Impossible de créer le répertoire '.$directory.' pour y télécharger les données anonymisées de la caméra', DownloadException::ERROR);
        }

        if (false === ($outputFile = @fopen($outputCompleteFilename, 'w+'))) {
            throw new DownloadException('Impossible d’ouvrir le fichier '.$outputCompleteFilename.' pour y écrire les données à télécharger.', DownloadException::ERROR);
        }

        if (false === ($inputFile = @fopen($file, 'r'))) {
            throw new DownloadException('Impossible de lire le fichier distant '.$file, DownloadException::ERROR);
        }

        $images = [];
        $row = 1;
        while (false !== ($data = fgetcsv($inputFile, 0, "\t"))) {
            if (null === $data) {
                continue;
            }
            //On crée une nouvelle colonne, on doit le faire AVANT le cryptage de la plaque.
            if (1 == $row) {
                //Première ligne j'ajoute les entêtes
                $line = $this->header($data);
                fwrite($outputFile, implode("\t", $line)."\r\n");
                unset($line);
            }
            $line = $this->format($data);
            fwrite($outputFile, implode("\t", $line)."\r\n");
            if (key_exists('F', $line) && key_exists('Fc', $line)) {
                $images[$row] = [$line['F'], $line['Fc']];
            }else {
                throw new DownloadException('Colonnes erronées dans '.$file.'. Soit le téléchargement a été interrompu, soit le FW de la caméra n’est pas connu.', DownloadException::ERROR);
            }
            ++$row;
            unset($line, $data);
        }
        fclose($inputFile);
        fclose($outputFile);

        return $images;
    }

    /**
     * Crée les entêtes du fichier CSV.
     *
     * @param array $data
     *
     * @return array
     *
     * @throws DownloadException
     */
    private function header(array $data): array
    {
        $resultat = [];
        foreach ($data as $line) {
            list($key) = explode('=', $line);
            $resultat[] = $key;
        }

        if (count($data) !== count($resultat)) {
            throw new DownloadException('Erreur durant la création des entêtes : le nombre de colonne dans les données ne correspond pas au nombre de colonnes dans les résultats.');
        }

        return $resultat;
    }

    /**
     * Formate les données.
     *
     * @param array $data
     *
     * @return array
     */
    private function format(array $data): array
    {
        $resultat = [];
        $cryptage = 'sha1';

        foreach ($data as $line) {
            $tampon = "";
            list($key, $value) = explode('=', $line);
            switch ($key) {
                case 'P':
                    $resultat[$key] = $cryptage($value.'un sel crystallisé');
                    break;
                case 'p':
                    $resultat[$key] = $cryptage($value.'un sel crystallisé');
                    $tampon = $cryptage($this->simplify($value).'un sel crystallisé');
                    break;
                default:
                    $resultat[$key] = $value;
            }
            $resultat['PC'] = $tampon;
        }

        if ((count($data)+1) !== count($resultat)) {
            die('error');
        }

        return $resultat;
    }

    /**
     * @param string $image
     * @param string $code
     * @param string $url
     *
     * @return bool
     *
     * @throws DownloadException
     */
    public function downloadImage(string $image, string $code, string $url): ?bool
    {
        $subdirectory = dirname($image);

        $mydirectory = __DIR__."/../../data/downloaded/camera-$code/images/$subdirectory/";

        if (!is_dir($mydirectory) && false === @mkdir($mydirectory, 0755, true)) {
            throw new DownloadException('Impossible de créer le répertoire '.$mydirectory.' pour y stocker les photos de la caméra');
        }

        $originFile = $url.$image;
        $destinationFile = $mydirectory.basename($image);

        if (file_exists($destinationFile)) {
            //We skip it !
            return null;
        }

        return @copy($originFile, $destinationFile);
    }

    /**
     * @param string $immatriculation
     *
     * @return string
     */
    public function simplify(string $immatriculation): string
    {
        $convert = [
            'O' => '0',
            'Q' => '0',
            'I' => '1',
            'S' => '5',
            'B' => '8',
            '-' => '',
        ];

        return strtr($immatriculation, $convert);
    }
}
