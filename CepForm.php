<?php

class CepForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'end';
    private static $activeRecord = 'Cep';
    private static $primaryKey = 'id';
    private static $formName = 'form_Cep';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de cep");


        $id = new TEntry('id');
        $cep = new TEntry('cep');
        $logradouro = new TEntry('logradouro');
        $bairro = new TEntry('bairro');
        $cidade = new TEntry('cidade');
        $uf = new TEntry('uf');

        $cep->setExitAction(new TAction([$this,'onExit']));

        $id->setEditable(false);
        $id->setSize(100);
        $uf->setSize('16%');
        $cep->setSize('16%');
        $bairro->setSize('16%');
        $cidade->setSize('16%');
        $logradouro->setSize('16%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel("Cep:", null, '14px', null)],[$cep]);
        $row3 = $this->form->addFields([new TLabel("Logradouro:", null, '14px', null)],[$logradouro]);
        $row4 = $this->form->addFields([new TLabel("Bairro:", null, '14px', null)],[$bairro]);
        $row5 = $this->form->addFields([new TLabel("Cidade:", null, '14px', null)],[$cidade]);
        $row6 = $this->form->addFields([new TLabel("Uf:", null, '14px', null)],[$uf]);

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(["Básico","Cadastro de cep"]));
        $container->add($this->form);

        parent::add($container);

    }

    public static function onExit($param = null) 
    {
        try 
        {
        $ resultado  =  @ file_get_contents ( ' http://republicavirtual.com.br/web_cep.php?cep= '  .  urlencode ( $ param [ ' cep ' ]) .  ' & formato = query_string ' );
        if ( ! $ resultado ) {
            $ resultado  =  " & resultado = 0 & resultado_txt = erro + ao + buscar + cep " ;
        }
        parse_str ( $ resultado , $ retorno );
        $ obj  =  novo  StdClass ;
        $ obj -> cep  =  $ param [ ' cep ' ];
        $ obj -> logradouro  =  strtoupper ( $ retorno [ ' tipo_logradouro ' ] .  '  '  .  $ retorno [ ' logradouro ' ]);
        $ obj -> bairro  =  strtoupper ( $ retorno [ ' bairro ' ]);
        $ obj -> cidade  =  strtoupper ( $ retorno [ ' cidade ' ]);
        $ obj -> uf  =  strtoupper ( $ retorno [ ' uf ' ]);
        TForm :: sendData ( ' form_Cep ' , $ obj );

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Cep(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            /**
            // To define an action to be executed on the message close event:
            $messageAction = new TAction(['className', 'methodName']);
            **/

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $messageAction);

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Cep($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

    } 

}

