<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Plugins
 *
 * @ORM\Table(name="plugins", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"}), @ORM\UniqueConstraint(name="folder", columns={"mainFile"})})
 * @ORM\Entity
 */
class Plugin
{
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
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type = 'exe';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="mainFile", type="string", nullable=false)
     */
    private $mainfile;

    /**
     * @ORM\Column(name="identifier", type="string")
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="config", type="text", nullable=false)
     */
    private $config;

    /**
     * @ORM\OneToMany(targetEntity="PluginTest", mappedBy="plugin")
     */
    private $pluginTests;



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
     * Set name
     *
     * @param string $name
     * @return Plugin
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Plugin
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Plugin
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
     * Set mainfile
     *
     * @param string $mainfile
     * @return Plugin
     */
    public function setMainfile($mainfile)
    {
        $this->mainfile = $mainfile;

        return $this;
    }

    /**
     * Get mainfile
     *
     * @return string 
     */
    public function getMainfile()
    {
        return $this->mainfile;
    }

    /**
     * Set config
     *
     * @param string $config
     * @return Plugin
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
     * Constructor
     */
    public function __construct()
    {
        $this->pluginTests = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add pluginTests
     *
     * @param \PluginTest $pluginTests
     * @return Plugin
     */
    public function addPluginTest(\PluginTest $pluginTests)
    {
        $this->pluginTests[] = $pluginTests;

        return $this;
    }

    /**
     * Remove pluginTests
     *
     * @param \PluginTest $pluginTests
     */
    public function removePluginTest(\PluginTest $pluginTests)
    {
        $this->pluginTests->removeElement($pluginTests);
    }

    /**
     * Get pluginTests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPluginTests()
    {
        return $this->pluginTests;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return Plugin
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string 
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
