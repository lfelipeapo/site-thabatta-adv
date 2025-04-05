=== Arquivos de Tradução para o Tema Thabatta Advocacia ===

Este diretório contém os arquivos de tradução para o tema Thabatta Advocacia.

== Arquivos ==
* thabatta-adv.pot - Arquivo de modelo para tradução (Template)
* thabatta-adv-pt_BR.po - Arquivo de tradução para português do Brasil
* thabatta-adv-pt_BR.mo - Arquivo compilado de tradução para português do Brasil

== Como adicionar uma nova tradução ==
1. Copie o arquivo thabatta-adv.pot para um novo arquivo chamado thabatta-adv-LOCALE.po
   (substitua LOCALE pelo código do idioma, por exemplo: en_US, es_ES, fr_FR, etc.)
2. Edite o arquivo .po com um editor de arquivos PO como o Poedit (https://poedit.net/)
3. Traduza todas as strings
4. Salve o arquivo e o Poedit gerará automaticamente o arquivo .mo correspondente
5. Alternativamennte, você pode compilar o arquivo .mo usando o comando:
   msgfmt -o thabatta-adv-LOCALE.mo thabatta-adv-LOCALE.po

== Como atualizar o arquivo .pot ==
Se você adicionou novas strings ao tema, é necessário atualizar o arquivo .pot:

1. Instale o WP-CLI (https://wp-cli.org/)
2. Execute o comando:
   wp i18n make-pot /caminho/para/o/tema/thabatta-adv-theme/ /caminho/para/o/tema/thabatta-adv-theme/languages/thabatta-adv.pot --domain=thabatta-adv

== Como usar as traduções no tema ==
O tema carrega automaticamente o arquivo de tradução correto com base no idioma do WordPress.
Para mudar o idioma do WordPress, vá para Configurações > Geral e selecione o idioma desejado. 