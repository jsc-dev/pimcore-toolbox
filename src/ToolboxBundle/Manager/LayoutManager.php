<?php

namespace ToolboxBundle\Manager;

use Symfony\Component\Templating\EngineInterface;

class LayoutManager
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;
        return $this;
    }

    /**
     * @param null $areaId
     * @param string $viewName
     * @param string $extension
     *
     * @return string
     */
    public function getAreaTemplateDir($areaId = NULL, $viewName = 'view', $extension = 'html.twig')
    {
        $elementThemeConfig = $this->getAreaThemeConfig($areaId);

        $pathStructure = '@Toolbox/Toolbox/%s/%s';

        $defaultDir = sprintf($pathStructure,
            $elementThemeConfig['layout'],
            ucfirst($areaId)
        );

        //no fallback layout defined. return default.
        if($elementThemeConfig['default_layout'] === FALSE || empty($elementThemeConfig['default_layout'])) {
            return $defaultDir;
        }

        if($this->templating->exists($defaultDir . DIRECTORY_SEPARATOR . $viewName . '.' . $extension)) {
            return $defaultDir;
        }

        //return fallback layout.
        return sprintf($pathStructure,
            $elementThemeConfig['default_layout'],
            ucfirst($areaId)
        );
    }

    /**
     * @param null   $areaId
     * @param string $viewName
     * @param string $extension
     *
     * @return string
     */
    public function getAreaTemplatePath($areaId = NULL, $viewName = 'view', $extension = 'html.twig')
    {
        return $this->getAreaTemplateDir($areaId, $viewName) . DIRECTORY_SEPARATOR . $viewName . '.' . $extension;
    }

    /**
     * @param string $areaName
     *
     * @return array|bool
     */
    public function getAreaThemeConfig($areaName = '')
    {
        $layoutConfiguration = $this->configManager->setAreaNameSpace(ConfigManager::AREABRICK_NAMESPACE_INTERNAL)->getConfig('theme');

        $theme = [
            'layout'         => $layoutConfiguration['layout'],
            'default_layout' => $layoutConfiguration['default_layout'],
            'wrapper'        => FALSE
        ];

        if (isset($layoutConfiguration['wrapper'][$areaName]['wrapper_classes'])) {
            $theme['wrapper'] = $layoutConfiguration['wrapper'][$areaName]['wrapper_classes'];
        }

        return $theme;
    }
}