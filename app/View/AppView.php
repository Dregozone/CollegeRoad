<?php


namespace app\View;


class AppView
{
    protected $page;

    public function __construct($page) {
        $this->page = $page;

        echo $this->startHtml();
    }

    public function __destruct() {

        echo $this->endHtml();
    }

    private function startHtml() {

        $html = '
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <title>' . ucfirst($this->page) . ' - College Road Swimming Club</title>
                    
                    <!-- meta desc, keyword, author, viewport, charset?, favicon, service worker?, pwa stuff? -->
                    
                    <link rel="stylesheet" type="text/css" href="public/css/Shared.css" />
                    <link rel="stylesheet" type="text/css" href="public/css/' . $this->page . '.css" />
                </head>
                <body>
        ';

        $html .= $this->header();

        if (
            (strtoupper($this->page) != "LOGIN") &&
            (strtoupper($this->page) != "LOGOUT") &&
            (strtoupper($this->page) != "REGISTER")
        ) {
            $html .= $this->nav();
        }

        return $html;
    }

    private function header() {

        $html = '
            <header style="display: flex; justify-content: center; align-content: center;">
                <div style="width: 20%;"></div>
                
                <div style="width: 60%;">
                    <h1>
                        College Road Swim Club
                    </h1>
                </div>

                <img src="public/img/CollegeRoadLogo.png" class="logo" style="width: 126px; height: 95px; margin: 1% 0;" />

            </header>
        ';

        return $html;
    }

    private function footer() {

        $html = '
            <footer>
                &copy; Copyright ' . date('Y') . '
            </footer>
        ';

        return $html;
    }

    private function nav() {

        $html = '
            <nav>
                <div style="width: 10%;">
                    <a href="?p=Details">
                        Personal details
                    </a>
                </div>
                
                <div style="width: 10%;">
                    <a href="?p=Data">
                        Race data
                    </a>
                </div>
                
                <div style="width: 10%;">
        ';

        if ( $this->model->getIsAdmin() || $this->model->getIsCoach() || $this->model->getIsOfficial() ) {
            $html .= '        
                    <a href="?p=Admin">
                        Admin
                    </a>
            ';
        }

        $html .= '
                </div>   
        
                <div style="width: 50%;"></div> <!-- SPACER -->
                
                <div style="width: 10%;">
                    Welcome, ' . ucfirst($_SESSION["user"]) . '
                </div>
                
                <div style="width: 10%;">
                    <a href="?p=Logout">
                        Logout
                    </a>
                </div>
            </nav>
        ';

        return $html;
    }

    private function endHtml() {

        $html = '
                    ' . $this->footer() . '
                    <script src="public/js/Shared.js"></script>
                    <script src="public/js/' . $this->page . '.js"></script>
                </body>
            </html>
        ';

        return $html;
    }

    /** Helpful for debugging
     *
     *
     */
    public function console($msg) {

        $html = '
            <script>
                console.log("' . $msg . '")
            </script>
        ';

        return $html;
    }

    /** Helpful for debugging
     *
     *
     */
    public function alert($msg) {

        $html = '
            <script>
                alert("' . $msg . '")
            </script>
        ';

        return $html;
    }
    public function h($string) {

        return htmlspecialchars(trim($string), ENT_QUOTES);
    }
}
