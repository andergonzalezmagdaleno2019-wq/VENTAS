<div class="has-text-centered mb-2 mt-2"> 
    <figure class="image is-inline-block" style="width: 180px; margin-bottom: 0;"> <?php 
            $path_logo = "./app/views/img/logo.png";
            $path_black = "./app/views/img/logo_black.png";
            
            if(is_file($path_logo)): ?>
                <img src="<?php echo APP_URL; ?>app/views/img/logo.png?v=<?php echo time(); ?>" 
                     class="logo-light" 
                     style="height: auto; width: 100%;">
                
                <?php if(is_file($path_black)): ?>
                    <img src="<?php echo APP_URL; ?>app/views/img/logo_black.png?v=<?php echo time(); ?>" 
                         class="logo-dark" 
                         style="display: none; height: auto; width: 100%;">
                <?php else: ?>
                    <script>console.warn("Fasnet: Falta el archivo logo_black.png en app/views/img/");</script>
                <?php endif; ?>
                
        <?php else: ?>
            <img src="<?php echo APP_URL; ?>app/views/img/default.png" style="width: 100px;">
        <?php endif; ?>
    </figure>
</div>