<?php
    function verificarSession(){
        if(isset($_SESSION['usuario'])) {
            return true;
        }
        header('Location: /');
    }

    function opciones_usuario(){
       
        echo '<ul>
                <li>
                    <a href="./app-profile.html" class="dropdown-item ai-icon">
                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span class="ml-2">Mi usuario </span>
                    </a>
                </li>
                <li>
                    <a href="/" class="dropdown-item ai-icon">
                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span class="ml-2">Cerrar Sesion </span>
                    </a>
                </li>
            </ul>';
    }
    
    function menu() {
        global $TITTLE;
        global $TITTLE_COLOR;
        if($TITTLE != ''){
            $pruebas ='
                <li>
                    <a>
                        <i class="lni lni-warning" style="color:'.$TITTLE_COLOR.'" data-toggle="tooltip" data-original-title="'.$TITTLE.'" data-placement="right"></i><span class="nav-text " style="color:'.$TITTLE_COLOR.'">'.$TITTLE.'</span>
                    </a>
                </li>
                ';
        }
        echo ' 		
        <div class="deznav">
            <div class="deznav-scroll bg-success-light ps mm-active">
                <ul class="metismenu" id="menu">
                        '.$pruebas.'
                        <li>
                            <a href="pacientes.php" class="ai-icon" aria-expanded="false" data-toggle="tooltip" data-original-title="pacientes" data-placement="right" >
                                <i class="fa fa-users text-dark"></i>
                                <span class="nav-text">Pacientes</span>
                            </a>
                        </li>
                </ul>
            </div>
        </div>            
        ';
        
    }

    function linksFooter(){
        echo '
        <!-- Select2 -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="js/funciones.js"></script>
        <!-- Toastr -->
        <script src="./vendor/toastr/js/toastr.min.js"></script>
        ';
    }

?>