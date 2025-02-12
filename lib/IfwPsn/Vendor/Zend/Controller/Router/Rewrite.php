<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Rewrite.php 2910885 2023-05-10 20:44:02Z worschtebrot $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** IfwPsn_Vendor_Zend_Controller_Router_Abstract */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Abstract.php';

/** IfwPsn_Vendor_Zend_Controller_Router_Route */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Route.php';

/**
 * Ruby routing based Router.
 *
 * @package    IfwPsn_Vendor_Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class IfwPsn_Vendor_Zend_Controller_Router_Rewrite extends IfwPsn_Vendor_Zend_Controller_Router_Abstract
{

    /**
     * Whether or not to use default routes
     *
     * @var boolean
     */
    protected $_useDefaultRoutes = true;

    /**
     * Array of routes to match against
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * Currently matched route
     *
     * @var string
     */
    protected $_currentRoute = null;

    /**
     * Global parameters given to all routes
     *
     * @var array
     */
    protected $_globalParams = array();

    /**
     * Separator to use with chain names
     *
     * @var string
     */
    protected $_chainNameSeparator = '-';

    /**
     * Determines if request parameters should be used as global parameters
     * inside this router.
     *
     * @var boolean
     */
    protected $_useCurrentParamsAsGlobal = false;

    /**
     * Add default routes which are used to mimic basic router behaviour
     *
     * @return IfwPsn_Vendor_Zend_Controller_Router_Rewrite
     */
    public function addDefaultRoutes()
    {
        if (!$this->hasRoute('default')) {
            $dispatcher = $this->getFrontController()->getDispatcher();
            $request    = $this->getFrontController()->getRequest();

            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Route/Module.php';
            $compat = new IfwPsn_Vendor_Zend_Controller_Router_Route_Module(array(), $dispatcher, $request);

            $this->_routes = array('default' => $compat) + $this->_routes;
        }

        return $this;
    }

    /**
     * Add route to the route chain
     *
     * If route contains method setRequest(), it is initialized with a request object
     *
     * @param  string                                 $name  Name of the route
     * @param  IfwPsn_Vendor_Zend_Controller_Router_Route_Interface $route Instance of the route
     * @return IfwPsn_Vendor_Zend_Controller_Router_Rewrite
     */
    public function addRoute($name, IfwPsn_Vendor_Zend_Controller_Router_Route_Interface $route)
    {
        if (method_exists($route, 'setRequest')) {
            $route->setRequest($this->getFrontController()->getRequest());
        }

        $this->_routes[$name] = $route;

        return $this;
    }

    /**
     * Add routes to the route chain
     *
     * @param  array $routes Array of routes with names as keys and routes as values
     * @return IfwPsn_Vendor_Zend_Controller_Router_Rewrite
     */
    public function addRoutes($routes)
    {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }

        return $this;
    }

    /**
     * Create routes out of IfwPsn_Vendor_Zend_Config configuration
     *
     * Example INI:
     * routes.archive.route = "archive/:year/*"
     * routes.archive.defaults.controller = archive
     * routes.archive.defaults.action = show
     * routes.archive.defaults.year = 2000
     * routes.archive.reqs.year = "\d+"
     *
     * routes.news.type = "IfwPsn_Vendor_Zend_Controller_Router_Route_Static"
     * routes.news.route = "news"
     * routes.news.defaults.controller = "news"
     * routes.news.defaults.action = "list"
     *
     * And finally after you have created a IfwPsn_Vendor_Zend_Config with above ini:
     * $router = new IfwPsn_Vendor_Zend_Controller_Router_Rewrite();
     * $router->addConfig($config, 'routes');
     *
     * @param  IfwPsn_Vendor_Zend_Config $config  Configuration object
     * @param  string      $section Name of the config section containing route's definitions
     * @throws IfwPsn_Vendor_Zend_Controller_Router_Exception
     * @return IfwPsn_Vendor_Zend_Controller_Router_Rewrite
     */
    public function addConfig(IfwPsn_Vendor_Zend_Config $config, $section = null)
    {
        if ($section !== null) {
            if ($config->{$section} === null) {
                require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Exception.php';
                throw new IfwPsn_Vendor_Zend_Controller_Router_Exception("No route configuration in section '{$section}'");
            }

            $config = $config->{$section};
        }

        foreach ($config as $name => $info) {
            $route = $this->_getRouteFromConfig($info);

            if ($route instanceof IfwPsn_Vendor_Zend_Controller_Router_Route_Chain) {
                if (!isset($info->chain)) {
                    require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Exception.php';
                    throw new IfwPsn_Vendor_Zend_Controller_Router_Exception("No chain defined");
                }

                if ($info->chain instanceof IfwPsn_Vendor_Zend_Config) {
                    $childRouteNames = $info->chain;
                } else {
                    $childRouteNames = explode(',', $info->chain);
                }

                foreach ($childRouteNames as $childRouteName) {
                    $childRoute = $this->getRoute(trim($childRouteName));
                    $route->chain($childRoute);
                }

                $this->addRoute($name, $route);
            } elseif (isset($info->chains) && $info->chains instanceof IfwPsn_Vendor_Zend_Config) {
                $this->_addChainRoutesFromConfig($name, $route, $info->chains);
            } else {
                $this->addRoute($name, $route);
            }
        }

        return $this;
    }

    /**
     * Get a route frm a config instance
     *
     * @param  IfwPsn_Vendor_Zend_Config $info
     * @return IfwPsn_Vendor_Zend_Controller_Router_Route_Interface
     */
    protected function _getRouteFromConfig(IfwPsn_Vendor_Zend_Config $info)
    {
        $class = (isset($info->type)) ? $info->type : 'IfwPsn_Vendor_Zend_Controller_Router_Route';
        if (!class_exists($class)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Zend/Loader.php';
            IfwPsn_Zend_Loader::loadClass($class);
        }

        $route = call_user_func(
            array(
                $class,
                'getInstance'
            ), $info
        );

        if (isset($info->abstract) && $info->abstract && method_exists($route, 'isAbstract')) {
            $route->isAbstract(true);
        }

        return $route;
    }

    /**
     * Add chain routes from a config route
     *
     * @param  string                                 $name
     * @param  IfwPsn_Vendor_Zend_Controller_Router_Route_Interface $route
     * @param  IfwPsn_Vendor_Zend_Config                            $childRoutesInfo
     * @return void
     */
    protected function _addChainRoutesFromConfig(
        $name,
        IfwPsn_Vendor_Zend_Controller_Router_Route_Interface $route,
        IfwPsn_Vendor_Zend_Config $childRoutesInfo
    )
    {
        foreach ($childRoutesInfo as $childRouteName => $childRouteInfo) {
            if (is_string($childRouteInfo)) {
                $childRouteName = $childRouteInfo;
                $childRoute     = $this->getRoute($childRouteName);
            } else {
                $childRoute = $this->_getRouteFromConfig($childRouteInfo);
            }

            if ($route instanceof IfwPsn_Vendor_Zend_Controller_Router_Route_Chain) {
                $chainRoute = clone $route;
                $chainRoute->chain($childRoute);
            } else {
                $chainRoute = $route->chain($childRoute);
            }

            $chainName = $name . $this->_chainNameSeparator . $childRouteName;

            if (isset($childRouteInfo->chains)) {
                $this->_addChainRoutesFromConfig($chainName, $chainRoute, $childRouteInfo->chains);
            } else {
                $this->addRoute($chainName, $chainRoute);
            }
        }
    }

    /**
     * Remove a route from the route chain
     *
     * @param  string $name Name of the route
     * @throws IfwPsn_Vendor_Zend_Controller_Router_Exception
     * @return IfwPsn_Vendor_Zend_Controller_Router_Rewrite
     */
    public function removeRoute($name)
    {
        if (!isset($this->_routes[$name])) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Exception.php';
            throw new IfwPsn_Vendor_Zend_Controller_Router_Exception("Route $name is not defined");
        }

        unset($this->_routes[$name]);

        return $this;
    }

    /**
     * Remove all standard default routes
     *
     * @return IfwPsn_Vendor_Zend_Controller_Router_Rewrite
     */
    public function removeDefaultRoutes()
    {
        $this->_useDefaultRoutes = false;

        return $this;
    }

    /**
     * Check if named route exists
     *
     * @param  string $name Name of the route
     * @return boolean
     */
    public function hasRoute($name)
    {
        return isset($this->_routes[$name]);
    }

    /**
     * Retrieve a named route
     *
     * @param string $name Name of the route
     * @throws IfwPsn_Vendor_Zend_Controller_Router_Exception
     * @return IfwPsn_Vendor_Zend_Controller_Router_Route_Interface Route object
     */
    public function getRoute($name)
    {
        if (!isset($this->_routes[$name])) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Exception.php';
            throw new IfwPsn_Vendor_Zend_Controller_Router_Exception("Route $name is not defined");
        }

        return $this->_routes[$name];
    }

    /**
     * Retrieve a currently matched route
     *
     * @throws IfwPsn_Vendor_Zend_Controller_Router_Exception
     * @return IfwPsn_Vendor_Zend_Controller_Router_Route_Interface Route object
     */
    public function getCurrentRoute()
    {
        if (!isset($this->_currentRoute)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Exception.php';
            throw new IfwPsn_Vendor_Zend_Controller_Router_Exception("Current route is not defined");
        }

        return $this->getRoute($this->_currentRoute);
    }

    /**
     * Retrieve a name of currently matched route
     *
     * @throws IfwPsn_Vendor_Zend_Controller_Router_Exception
     * @return string Route name
     */
    public function getCurrentRouteName()
    {
        if (!isset($this->_currentRoute)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Exception.php';
            throw new IfwPsn_Vendor_Zend_Controller_Router_Exception("Current route is not defined");
        }

        return $this->_currentRoute;
    }

    /**
     * Retrieve an array of routes added to the route chain
     *
     * @return array All of the defined routes
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Find a matching route to the current PATH_INFO and inject
     * returning values to the Request object.
     *
     * @param IfwPsn_Vendor_Zend_Controller_Request_Abstract $request
     * @throws IfwPsn_Vendor_Zend_Controller_Router_Exception
     * @return IfwPsn_Vendor_Zend_Controller_Request_Abstract Request object
     */
    public function route(IfwPsn_Vendor_Zend_Controller_Request_Abstract $request)
    {
        if (!$request instanceof IfwPsn_Vendor_Zend_Controller_Request_Http) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Exception.php';
            throw new IfwPsn_Vendor_Zend_Controller_Router_Exception(
                'IfwPsn_Vendor_Zend_Controller_Router_Rewrite requires a IfwPsn_Vendor_Zend_Controller_Request_Http-based request object'
            );
        }

        if ($this->_useDefaultRoutes) {
            $this->addDefaultRoutes();
        }

        // Find the matching route
        $routeMatched = false;

        foreach (array_reverse($this->_routes, true) as $name => $route) {
            // TODO: Should be an interface method. Hack for 1.0 BC
            if (method_exists($route, 'isAbstract') && $route->isAbstract()) {
                continue;
            }

            // TODO: Should be an interface method. Hack for 1.0 BC
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $request->getPathInfo();
            } else {
                $match = $request;
            }

            if ($params = $route->match($match)) {
                $this->_setRequestParams($request, $params);
                $this->_currentRoute = $name;
                $routeMatched        = true;
                break;
            }
        }

        if (!$routeMatched) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Exception.php';
            throw new IfwPsn_Vendor_Zend_Controller_Router_Exception('No route matched the request', 404);
        }

        if ($this->_useCurrentParamsAsGlobal) {
            $params = $request->getParams();
            foreach ($params as $param => $value) {
                $this->setGlobalParam($param, $value);
            }
        }

        return $request;
    }

    /**
     * Sets parameters for request object
     *
     * Module name, controller name and action name
     *
     * @param IfwPsn_Vendor_Zend_Controller_Request_Abstract $request
     * @param array                            $params
     */
    protected function _setRequestParams($request, $params)
    {
        foreach ($params as $param => $value) {

            $request->setParam($param, $value);

            if ($param === $request->getModuleKey()) {
                $request->setModuleName($value);
            }
            if ($param === $request->getControllerKey()) {
                $request->setControllerName($value);
            }
            if ($param === $request->getActionKey()) {
                $request->setActionName($value);
            }
        }
    }

    /**
     * Generates a URL path that can be used in URL creation, redirection, etc.
     *
     * @param  array $userParams Options passed by a user used to override parameters
     * @param  mixed $name       The name of a Route to use
     * @param  bool  $reset      Whether to reset to the route defaults ignoring URL params
     * @param  bool  $encode     Tells to encode URL parts on output
     * @throws IfwPsn_Vendor_Zend_Controller_Router_Exception
     * @return string Resulting absolute URL path
     */
    public function assemble($userParams, $name = null, $reset = false, $encode = true)
    {
        if (!is_array($userParams)) {
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Router/Exception.php';
            throw new IfwPsn_Vendor_Zend_Controller_Router_Exception('userParams must be an array');
        }

        if ($name == null) {
            try {
                $name = $this->getCurrentRouteName();
            } catch (IfwPsn_Vendor_Zend_Controller_Router_Exception $e) {
                $name = 'default';
            }
        }

        // Use UNION (+) in order to preserve numeric keys
        $params = $userParams + $this->_globalParams;

        $route = $this->getRoute($name);
        $url   = $route->assemble($params, $reset, $encode);

        if (!preg_match('|^[a-z]+://|', $url)) {
            $url = rtrim($this->getFrontController()->getBaseUrl() ?? '', self::URI_DELIMITER) . self::URI_DELIMITER . $url;
        }

        return $url;
    }

    /**
     * Set a global parameter
     *
     * @param  string $name
     * @param  mixed  $value
     * @return IfwPsn_Vendor_Zend_Controller_Router_Rewrite
     */
    public function setGlobalParam($name, $value)
    {
        $this->_globalParams[$name] = $value;

        return $this;
    }

    /**
     * Set the separator to use with chain names
     *
     * @param string $separator The separator to use
     * @return IfwPsn_Vendor_Zend_Controller_Router_Rewrite
     */
    public function setChainNameSeparator($separator)
    {
        $this->_chainNameSeparator = $separator;

        return $this;
    }

    /**
     * Get the separator to use for chain names
     *
     * @return string
     */
    public function getChainNameSeparator()
    {
        return $this->_chainNameSeparator;
    }

    /**
     * Determines/returns whether to use the request parameters as global parameters.
     *
     * @param boolean|null $use
     *              Null/unset when you want to retrieve the current state.
     *              True when request parameters should be global, false otherwise
     * @return boolean|IfwPsn_Vendor_Zend_Controller_Router_Rewrite
     *              Returns a boolean if first param isn't set, returns an
     *              instance of IfwPsn_Vendor_Zend_Controller_Router_Rewrite otherwise.
     *
     */
    public function useRequestParametersAsGlobal($use = null)
    {
        if ($use === null) {
            return $this->_useCurrentParamsAsGlobal;
        }

        $this->_useCurrentParamsAsGlobal = (bool)$use;

        return $this;
    }
}
