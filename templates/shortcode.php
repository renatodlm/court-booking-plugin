<?php

$CourtManager = new CourtManager();
$sports       = $CourtManager->get_sports();

?>

<div class="courtContainer">
   <form id="courtForm">
      <div class="court-input">
         <label for="name">
            <?php

            esc_html_e('Nome', 'court-booking');

            ?>
         </label>
         <input type="text" id="name" name="name" placeholder="Digite seu nome" required>
      </div>

      <div class="court-input">
         <label for="email">
            <?php

            esc_html_e('E-mail', 'court-booking');

            ?>
         </label>
         <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
      </div>

      <div class="court-input">
         <label for="phone">
            <?php

            esc_html_e('Telefone', 'court-booking');

            ?>
         </label>
         <input type="text" id="phone" name="phone" maxlength="15" placeholder="(XX) XXXXX-XXXX" required>
      </div>

      <div class="court-input">
         <label for="rg">
            <?php

            esc_html_e('RG', 'court-booking');

            ?>
         </label>
         <input type="text" id="rg" maxlength="12" name="rg" placeholder="XX.XXX.XXX-X" required>
      </div>

      <div class="court-input">
         <input type="checkbox" id="free" name="free">
         <label for="free">
            <?php

            esc_html_e('É espaço livre?', 'court-booking');

            ?>
         </label>
      </div>

      <div class="court-select">
         <label for="sportSelect">
            <?php

            esc_html_e('O que deseja praticar?', 'court-booking');

            ?>
         </label>
         <select id="sportSelect" name="sportSelect" required>
            <option value="" selected disabled>
               <?php

               esc_html_e('Selecione uma quadra', 'court-booking');

               ?>
            </option>
            <?php foreach ($sports as $key => $sport)
            {
            ?>
               <option value="<?php echo $key; ?>"><?php echo 'Clínica de ' . $sport ?></option>
            <?php
            } ?>
         </select>
      </div>

      <div class="court-select">
         <label for="timeSelect">
            <?php

            esc_html_e('Horário', 'court-booking');

            ?>
         </label>
         <select id="timeSelect" name="timeSelect" required>
            <option value="" selected disabled>
               <?php

               esc_html_e('Selecione um Horário', 'court-booking');

               ?>
            </option>
         </select>
      </div>

      <button type="submit" class="court-button">
         <?php

         esc_html_e('Enviar', 'court-booking');

         ?>
      </button>
   </form>

</div>
