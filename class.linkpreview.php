<?php
 

class URLPreview
{
    
    var $description;
    var $title;
    var $image = array();
    var $url;
    var $html;
    
    function __construct($url)
    {
        
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url . '/';
        }
        
        $this->url = $url;
        $this->getHTML();
    }
    
    function getHTML()
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $this->html = curl_exec($ch);
        
        if (!$this->html) {
            
            echo 'Curl Error: ' . curl_error($ch);
            die();
        }
        curl_close($ch);
        $this->html = str_replace("<head>", "<head><base href=\"$this->url\">", $this->html);
    }
    
    function getDescription()
    {
        
        if (preg_match_all('/<meta(?=[^>]*name="description")\s[^>]*content="([^>]*)"/si', $this->html, $matches)) {
            
            foreach ($matches[1] as $key => $content) {
                
                echo $content;
            }
        } else if (preg_match_all('/<meta(?=[^>]*name="og:description")\s[^>]*content="([^>]*)"/si', $this->html, $matches)) {
            
            foreach ($matches[1] as $key => $content) {
                
                echo $content;
            }
        }
    }
    
    function getTitle()
    {
        
        if (preg_match("/<title>(.+)<\/title>/si", $this->html, $matches)) {
            
            echo $matches[1];
        } else {
            
            $dom = new DOMDocument;
            $dom->loadHTML($this->html);
            
            echo $dom->getElementsByTagName('title')->item(0)->nodeValue;
        }
    }
    
    function getImage($multiple = false)
    {
        
        /* First we will check if facebook opengraph image tag exist */
        
        if (preg_match_all('/<meta(?=[^>]*property="og:image")\s[^>]*content="([^>]*)"/si', $this->html, $matches)) {
            
            foreach ($matches[1] as $key => $content) {
                
                $image[] = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $content);
                if ($key == 5)
                    break;
            }
        }
        
        /* If not then we will get the first image from the html source */
        
        else if (preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $this->html, $matches)) {
            
            foreach ($matches[1] as $key => $value) {
                
                if (strpos($value, 'http') === false) {
                    
                    $image[] = $this->url . preg_replace("/&#?[a-z0-9]{2,8};/i", "", $value);
                } else {
                    
                    $image[] = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $value);
                }
                
                if ($key == 5)
                    break;
            }
        }
        
        $image_index = (isset($_GET['image_no'])) ? $_GET['image_no'] - 1 : 0;
        
        echo (!$multiple) ? $image[$image_index] : str_replace(array(
            "\\",
            "\"",
            " "
        ), array(
            "",
            "",
            ""
        ), json_encode($image));
    }
    
}

$URLPreview = new URLPreview($_GET['url']);
?>
<html>
    <head>
        <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>

    </head>
    <body>
        <table id="at_preview">
            <tbody>
                <tr>
                    <td width="100">
                        <img width="100" 
                             data-src = "<?php
                                        $URLPreview->getImage(1);
                                        ?>" 
                             src="<?php
                                $URLPreview->getImage();
                                ?>">
                    </td>
                    <td style="vertical-align: top;">
                        <span><strong><?php
                                        $URLPreview->getTitle();
                                        ?></strong></span>
                        <span style="display: block;margin-top: 8px;"><?php
                        $URLPreview->getDescription();
                        ?></span>
                    </td>
                </tr>
                <tr class="preview_footer">
                    <td colspan="2">
                        <span>Source:
                            <a href="<?php
                            echo $URLPreview->url;
                            ?>">
                <?php
                echo preg_replace('#^https?://#', '', $URLPreview->url);
                ?>
                           </a>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
        
    </body>
</html>