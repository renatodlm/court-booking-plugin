<!-- email-template.php -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <title>Confirmação de Registro</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
   <div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
      <h2 style="color: #4A90E2; text-align: center;">Confirmação de Registro</h2>
      <p style="font-size: 16px; text-align: center;">um registro foi realizado com sucesso.</p>
      <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
      <h3 style="color: #333;">Resumo do Formulário</h3>
      <table style="width: 100%; border-collapse: collapse;">
         <tr style="background-color: #f9f9f9;">
            <td style="padding: 10px; border: 1px solid #ddd;">Nome</td>
            <td style="padding: 10px; border: 1px solid #ddd;">{{name}}</td>
         </tr>
         <tr>
            <td style="padding: 10px; border: 1px solid #ddd;">E-mail</td>
            <td style="padding: 10px; border: 1px solid #ddd;">{{email}}</td>
         </tr>
         <tr style="background-color: #f9f9f9;">
            <td style="padding: 10px; border: 1px solid #ddd;">Telefone</td>
            <td style="padding: 10px; border: 1px solid #ddd;">{{phone}}</td>
         </tr>
         <tr>
            <td style="padding: 10px; border: 1px solid #ddd;">RG</td>
            <td style="padding: 10px; border: 1px solid #ddd;">{{rg}}</td>
         </tr>
         <tr style="background-color: #f9f9f9;">
            <td style="padding: 10px; border: 1px solid #ddd;">Esporte</td>
            <td style="padding: 10px; border: 1px solid #ddd;">{{sport}}</td>
         </tr>
         <tr>
            <td style="padding: 10px; border: 1px solid #ddd;">Horário</td>
            <td style="padding: 10px; border: 1px solid #ddd;">{{time_slot}}</td>
         </tr>
      </table>
   </div>
</body>

</html>
