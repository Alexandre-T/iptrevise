<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Twig;

/**
 * ProgressBar Extension.
 *
 * This class declare a Twig Function which translate a long (ProgressBar adress) and an integer (cidr) into an IP/Cidr
 *
 * @category Twig
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class ProgressBarExtension extends \Twig_Extension
{
    /**
     * Danger value.
     */
    const DANGER = 90;

    /**
     * Warning value.
     */
    const WARNING = 75;

    /**
     * Minimal value (to trigger the min-width).
     */
    const MINIMAL = 15;

    /**
     * Return the new Function: Progress Bar.
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return array(
            'progressbar' => new \Twig_SimpleFunction(
                'progressbar',
                [$this, 'progressBarFunction'],
                ['is_safe' => ['html']]
            ),
        );
    }

    /**
     * Display a Progress Bar .
     *
     * @param int $value
     * @param int $total
     *
     * @return string An html progressbar
     */
    public function progressBarFunction(int $value = 0, int $total = 100): string
    {
        $class = 'progress-bar progress-bar-striped ';
        $percent = (int) ($value / $total * 100);
        $text = twig_localized_number_filter($value).' / '.twig_localized_number_filter($total);

        //color of the progress-bar
        if ($percent > self::DANGER) {
            $class .= 'progress-bar-danger';
        } elseif ($percent > self::WARNING) {
            $class .= 'progress-bar-warning';
        } else {
            $class .= 'progress-bar-success';
        }

        //min-width
        if ($percent < self::MINIMAL) {
            $length = (int) (strlen($text) * 0.75);
            $style = "min-width: {$length}em";
        } else {
            $style = "width: $percent%";
        }

        return '<div class="progress"><div class="'.$class.'" role="progressbar" '
               .'aria-valuenow="'.$percent.'" '
               .'aria-valuemin="0" aria-valuemax="100" style="'.$style.'">'
               ."$text</div></div>";
    }

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'progress_bar_extension';
    }
}
