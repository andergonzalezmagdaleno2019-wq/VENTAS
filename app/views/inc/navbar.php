<div class="full-width navBar">
    <div class="full-width navBar-options">
        <i class="fas fa-exchange-alt fa-fw" id="btn-menu"></i> 
        <nav class="navBar-options-list">
            <ul class="list-unstyle">
                <li class="text-condensedLight noLink" >
                    <a class="btn-exit" href="<?php echo APP_URL."logOut/"; ?>" >
                        <i class="fas fa-power-off"></i>
                    </a>
                    <div class="navbar-item">
    <span class="tag is-info is-light is-medium" id="tasa-bcv-navbar" title="Tasa BCV Oficial">
        Calculando BCV...
    </span>
</div>
                </li>
                <li class="text-condensedLight noLink" >
                    <small><?php echo $_SESSION['usuario']; ?></small>
                </li>
                <li class="noLink">
                    <?php
                        if(is_file("./app/views/fotos/".$_SESSION['foto'])){
                            echo '<img class="is-rounded img-responsive" src="'.APP_URL.'app/views/fotos/'.$_SESSION['foto'].'">';
                        }else{
                            echo '<img class="is-rounded img-responsive" src="'.APP_URL.'app/views/fotos/default.png">';
                        }
                    ?>
                </li>
            </ul>
        </nav>
    </div>
</div>
<input type="hidden" id="anti_cache_input" value="no">
<script>
    window.addEventListener('pageshow', function(event) {
        let cacheInput = document.getElementById("anti_cache_input");
        let isCached = event.persisted || (window.performance && window.performance.navigation.type === 2);
        
        if (cacheInput.value === "yes" || isCached) {
            cacheInput.value = "no";
            window.location.reload();
        } else {
            cacheInput.value = "yes";
        }
    });
</script>