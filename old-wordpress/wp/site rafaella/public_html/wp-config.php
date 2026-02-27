<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', 'rafapontesdb' );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', 'rafapontesdb' );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', 'bii5CWRHpt7-Vt' );

/** Nome do host do MySQL */
define( 'DB_HOST', 'rafapontesdb.mysql.dbaas.com.br' );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define( 'DB_COLLATE', '' );

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'D%%.TFoE# &.kyk7+|l+S*Cltw2wW[?$V<~!tzk%u#iyx8tlc./F.oxW* Hn9UK*' );
define( 'SECURE_AUTH_KEY',  'iaqv))D$LTjSM4*eLEZJN~|p* ){9U0u3/Eq.y}LguX$a,1G$Xai9lyX6ZtcpF:J' );
define( 'LOGGED_IN_KEY',    'R/Q)L^7p-Pbpy-G~{qlLe;2{ifSH}ak~{@8JnLH6tbFXMpU*Y5?<_S=rZglei~2G' );
define( 'NONCE_KEY',        'nv~BSi0 1KuW&K3Sqasj;9w4^^;P(3i5tF;*D{*[#zS.wtEBA.4&0=bQ)UP4usYe' );
define( 'AUTH_SALT',        'p-;~GoNe?`v%CxYN#&[i)`s$:8^MaPp9-vIY-})5h5srt&iTp9X8sbp|ga%^@OQH' );
define( 'SECURE_AUTH_SALT', '/N~X[RB_P&Vs|}Ta#fQdvVZCo:|!);ocHw%A+%PY7Y=vB9*%sZ-3HA((^Q0Ajs<}' );
define( 'LOGGED_IN_SALT',   'm=30{e%(p#N?p(KtooHGQ_t0yXzm,@.MdQEG]PS<7`v}KTJEG5=p`xd4kop1D6If' );
define( 'NONCE_SALT',       'x);oJ(ZtlOrPJ|&[rgF5Flwq5#/]aYa85Zs[A9z}TRr<s Xa+NR((&.;v_Hy:Ld^' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Configura as variáveis e arquivos do WordPress. */
require_once ABSPATH . 'wp-settings.php';
