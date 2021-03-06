<?php
Yii::setPathOfAlias('webshell', dirname(__FILE__));

/**
 * Web shell module
 *
 * Allows you to run console commands from your browser. Can be useful for both no-ssh webservers and
 * console-style administration modules.
 *
 * See readme for installation instructions.
 *
 * @version 1.2
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @license BSD
 */
class WebShellModule extends HWebModule {
    /**
     * Path that will be loaded on 'exit' command
     *
     * @var string
     */
    public $exitUrl = '';

    /**
     * Default terminal options
     * @var array
     */
    private $defaultWtermOptions = array(
        'WIDTH' => '100%',
        'HEIGHT' => '100%',
        'WELCOME_MESSAGE' => 'Welcome to Yii web shell. Type <strong>help</strong> for the list of available commands.',
        'PS1' => '>',
        'TERMINAL_CLASS' => 'terminal',
        'PROMPT_CLASS' => 'prompt',
        'CONTENT_CLASS' => 'content',
        'THEME_CLASS_PREFIX' => 'theme_',
        'DEFAULT_THEME' => 'webshell',
        'HIGHLIGHT_CLASS' => 'highlighted',
        'KEYWORD_CLASS' => 'keyword',
    );

    /**
     * Terminal options
     * @var array
     */
    public $wtermOptions = array();

    public $commands = array();

    /**
	 * @var array mapping from command name to command configurations.
	 * Each command configuration can be either a string or an array.
	 * If the former, the string should be the file path of the command class.
	 * If the latter, the array must contain a 'class' element which specifies
	 * the command's class name or {@link YiiBase::getPathOfAlias class path alias}.
	 * The rest name-value pairs in the array are used to initialize
	 * the corresponding command properties. For example,
	 * <pre>
	 * array(
	 *   'email'=>array(
	 *      'class'=>'path.to.Mailer',
	 *      'interval'=>3600,
	 *   ),
	 *   'log'=>'path/to/LoggerCommand.php',
	 * )
	 * </pre>
	 */
    public $yiicCommandMap = array();

    /**
     * Set to false if you want to disable yiic
     * @var bool
     */
    public $useYiic = true;

    /**
	 * @var array the IP filters that specify which IP addresses are allowed to access web shell.
	 * Each array element represents a single filter. A filter can be either an IP address
	 * or an address with wildcard (e.g. 192.168.0.*) to represent a network segment.
	 * If you want to allow all IPs to access web shell, you may set this property to be false
	 * (DO NOT DO THIS UNLESS YOU KNOW THE CONSEQUENCE!!!)
	 * The default value is array('127.0.0.1', '::1'), which means web shell can only be accessed
	 * on the localhost.
	 */

    /**
     * @var callback A valid PHP callback that returns if user is allowed to use web shell
     *
     * Callback method accepts two parametes: controller and action.
     */
    public $checkAccessCallback;

    function init(){
        $this->wtermOptions = array_merge($this->defaultWtermOptions, $this->wtermOptions);

        if($this->useYiic)
            $this->registerCommand('yiic', array('yiic'), 'Allows to run <strong>yiic</strong> commands.');
        parent::init();
    }

    function registerCommand($name, $action, $helpText){
        $this->commands[$name] = array($action, $helpText);
    }

    function unregisterCommand($name){
        unset($this->commands[$name]);
    }
	
    public static function onAdminMenuInit($event)
    {
        $event->sender->addItem(array(
            'label' => Yii::t('WebShellModule.base', 'WebShell'),
            'url' => Yii::app()->createUrl('//webshell/default'),
            'icon' => '<i class="fa fa-align-left"></i>',
            'group' => 'manage',
            'sortOrder' => 1000,
            'isActive' => (Yii::app()->controller->module && Yii::app()->controller->module->id == 'webshell' && Yii::app()->controller->id == 'webshell'),
            'newItemCount' => 0
        ));
    }

    public static function onWebApplicationInit($event)
    {

        Yii::app()->setModules(array(
            'webshell' => array(
                'class' => 'system.webshell.WebShellModule',
                ),
        ));
    }
}
