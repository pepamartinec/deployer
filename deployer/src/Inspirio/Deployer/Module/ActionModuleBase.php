<?php
namespace Inspirio\Deployer\Module;

use Inspirio\Deployer\Config\ConfigAware;
use Inspirio\Deployer\ModuleBase;
use Inspirio\Deployer\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

/**
 * Base action implementation.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
abstract class ActionModuleBase extends ModuleBase implements ActionModuleInterface, ConfigAware
{
	/**
	 * @var \ReflectionObject
	 */
	private $reflection = null;

	/**
	 * @var string
	 */
	private $name = null;

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		if (!$this->name) {
			$name = $this->getReflection()->getShortName();
			$name = substr($name, 0, -6); // strip the Action suffix
			$name = lcfirst($name);

			$this->name = $name;
		}

		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		$title = $this->getName();
		$title = preg_replace('/[A-Z]/', ' $0', $title);
		$title = ucfirst($title);

		return $title;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isEnabled()
	{
		return true;
	}

    /**
     * {@inheritdoc}
     */
    public function render(Request $request, View $view)
    {
        $sections = $this->getSections();
        $sectionContent = array();

        foreach ($sections as $name => $title) {
            $sectionRenderer = 'render'. ucfirst($name);

            if (!method_exists($this, $sectionRenderer)) {
                $sectionContent[$name] = $this->renderTemplate($name, array());
                continue;
            }

            $sectionData = call_user_func(array($this, $sectionRenderer), $request);

            // returned 0/null/false - section disabled
            if (!$sectionData && !is_array($sectionData)) {
                continue;
            }

            // returned string - custom rendered section
            if (is_string($sectionData)) {
                $sectionContent[$name] = $sectionData;
                continue;
            }

            if ($sectionData === true) {
                $sectionData = array();
            }

//            $sectionContent[$name] = $this->renderTemplate($name, $sectionData);
        }

        return $view->render('module/layout.html.php', array(
            'sections'       => $sections,
            'sectionContent' => $sectionContent,
        ));
    }

    /**
     * List of module sections.
     *
     * @return array
     */
    abstract protected function getSections();


	/**
	 * Returns object reflection.
	 *
	 * @return \ReflectionObject
	 */
	private function getReflection()
	{
		if (!$this->reflection) {
			$this->reflection = new \ReflectionObject($this);
		}

		return $this->reflection;
	}
}
