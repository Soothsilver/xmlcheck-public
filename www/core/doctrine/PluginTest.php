<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * PluginTest
 *
 * @ORM\Table(name="tests", indexes={@ORM\Index(name="pluginId", columns={"pluginId"})})
 * @ORM\Entity
 */
class PluginTest
{
    const STATUS_COMPLETED = "completed";
    const STATUS_RUNNING = "running";
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=false)
     */
    private $description;

    /**
     * @var \Plugin
     * @ORM\ManyToOne(targetEntity="Plugin", inversedBy="pluginTests")
     * @ORM\JoinColumn(referencedColumnName="id", name="pluginId")
     */
    private $plugin;

    /**
     * @var string
     *
     * @ORM\Column(name="config", type="text", nullable=false)
     */
    private $config = '';

    /**
     * @var string
     *
     * @ORM\Column(name="input", type="string", nullable=false)
     */
    private $input = '';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_RUNNING;

    /**
     * @var integer
     *
     * @ORM\Column(name="success", type="integer", nullable=false)
     */
    private $success = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=false)
     */
    private $info = '';

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="string", nullable=false)
     */
    private $output = '';



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return PluginTest
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set config
     *
     * @param string $config
     * @return PluginTest
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return string 
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set input
     *
     * @param string $input
     * @return PluginTest
     */
    public function setInput($input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Get input
     *
     * @return string 
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return PluginTest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set success
     *
     * @param int $success
     * @return PluginTest
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success
     *
     * @return int
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return PluginTest
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set output
     *
     * @param string $output
     * @return PluginTest
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get output
     *
     * @return string 
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set plugin
     *
     * @param \Plugin $plugin
     * @return PluginTest
     */
    public function setPlugin(\Plugin $plugin = null)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return \Plugin 
     */
    public function getPlugin()
    {
        return $this->plugin;
    }
}
