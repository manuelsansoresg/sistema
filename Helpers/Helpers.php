<?php
    function headeradmin($data = "")
    {
        $view_header = "page/template/top.inc.php";
        require_once($view_header);
    }

    function footer($data = "")
    {
        $view_footer = "page/template/footer.php";
        require_once($view_footer);
    }
     function get_favicon()
    {
        $path = FAVICON; //path
        $favicon = SITE_FAVICON;
        $type = '';
        $href = '';
        $placeholder = '<link rel="shortcut icon" href="%s" type="%s">';

        switch (pathinfo($path .$favicon, PATHINFO_EXTENSION)) {
            case 'ico':
                $type = 'image/vnd.microsoft.icon';
                $href = $path .'/'. $favicon;
                break;
            case 'png':
                $type = 'image/png';
                $href = $path .'/'. $favicon;
                break;
            case 'gif':
                $type = 'image/gif';
                $href = $path .'/'. $favicon;
                break;
            case 'svg':
                $type = 'image/svg+xml';
                $href = $path .'/'. $favicon;
                break;
            case 'jpg':
                $type = 'image/jpg';
                $href = $path .'/'. $favicon;
                break;


            default:
                return false;
                break;
        }
         
        return sprintf($placeholder, $href, $type);
    }

    function debug($data)
    {
        $format = print_r('<pre>');
        $format .= print_r($data);
        $format .= print_r('</pre>');
        return $format;
    }

    function get_logo()
    {
        $default_logo = SITE_LOGO;
        $placeholder_image = 'https://via.placeholder.com/150x60';

        if (!is_file(IMAGE_PATH . $default_logo)) {
            return  $placeholder_image;
        }

        return IMG . $default_logo;
    }

    function limpiar($datos)
    {
        $datos = trim($datos);
        //$datos = htmlspecialchars($datos, ENT_QUOTES, 'UTF-8');
        //$datos = utf8_decode($datos);        
        $datos = utf8_to_iso8859_1($datos);
        
        return $datos;
    }

    function to_obj($array)
    {
        return json_decode(json_encode($array));
    }

    function now()
    {
        return date('Y-m-d H:i:s');
    }
    function utf8_to_iso8859_1(string $string): string {
        $s = (string) $string;
        $len = \strlen($s);

        for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) {
            switch ($s[$i] & "\xF0") {
                case "\xC0":
                case "\xD0":
                    $c = (\ord($s[$i] & "\x1F") << 6) | \ord($s[++$i] & "\x3F");
                    $s[$j] = $c < 256 ? \chr($c) : '?';
                    break;

                case "\xF0":
                    ++$i;
                    // no break

                case "\xE0":
                    $s[$j] = '?';
                    $i += 2;
                    break;

                default:
                    $s[$j] = $s[$i];
            }
        }

        return substr($s, 0, $j);
    }
    function caracterespecial(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
?>
