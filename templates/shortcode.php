<?php

$CourtManager = new CourtManager();
$sports       = $CourtManager->get_sports();

?>

<div class="courtContainer">
   <form id="courtForm">
      <div class="court-input">
         <label for="name">Nome</label>
         <input type="text" id="name" name="name" placeholder="Digite seu nome" required>
      </div>

      <div class="court-input">
         <label for="email">E-mail</label>
         <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
      </div>

      <div class="court-input">
         <label for="phone">Telefone</label>
         <input type="text" id="phone" name="phone" placeholder="Telefone" required>
      </div>

      <div class="court-input">
         <label for="rg">RG</label>
         <input type="text" id="rg" name="rg" placeholder="00.000.000-X" required>
      </div>

      <div class="court-select">
         <label for="sportSelect">O que deseja praticar?</label>
         <select id="sportSelect" name="sportSelect" required>
            <option value="">Selecione uma quadra</option>
            <?php foreach ($sports as $key => $sport)
            {
            ?>
               <option value="<?php echo $key; ?>"><?php echo $sport ?></option>
            <?php
            } ?>
         </select>
      </div>

      <div class="court-select">
         <label for="timeSelect">Horário</label>
         <select id="timeSelect" name="timeSelect" required>
            <option value="">Selecione um Horário</option>
         </select>
      </div>

      <button type="submit" class="court-button">Enviar</button>
   </form>

</div>
