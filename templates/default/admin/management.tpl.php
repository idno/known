
<div class="row">

    <div class="span10 offset1">
        <h1>User Mangement</h1>
        <?=$this->draw('admin/menu')?>
    
<div class="explanation">
            <p>
                This is a user management system.
          </p>
<div>
<?php  
    db.idno.find( { type: 'user' } )
    ?>
</div>

        </div>
        </div>
</div>
