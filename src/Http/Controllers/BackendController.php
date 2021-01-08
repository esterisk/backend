<?php
namespace Esterisk\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class BackendController extends BaseController
{

		private function backend($backend) 
		{
			if (class_exists($class = config('backend.backends.'.$backend.'.class'))) return new $class($backend);
			else abort(404);
		}

    /**
     * Switch get request
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request, $backend)
    {
    		$backend = $this->backend($backend);
    		dd($backend);
// qui dovrebbe tornare la dashboard

//    		try {
//	    		return $backend->execute($command, $id, $request);
//	    	} catch (\Exception $e) {
//	    		abort(404);
//	    	}
    }

    /**
     * Switch get request
     *
     * @return \Illuminate\Http\Response
     */
    public function getRouter(Request $request, $backend, $resource, $command = 'list', $id = null)
    {
    	$backend = $this->backend($backend);
//    	try {
	    	return $backend->execute($resource, $command, $id, $request);
//	    } catch (\Exception $e) {
//	    	abort(404);
//	    }
    }

    /**
     * Switch post request
     *
     * @return \Illuminate\Http\Response
     */
    public function postRouter(Request $request, $backend, $resource, $command, $id = null)
    {
    		$backend = $this->backend($backend);

//    		try {
	    		return $backend->execute($resource, $command, $id, $request);
//	    	} catch (\Exception $e) {
//	    		abort(404);
//	    	}
    }

    /**
     * Switch ajax request
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxRouter(Request $request, $backend, $resource, $command, $id = null)
    {
    		$set = trim($request->route()->getCompiled()->getStaticPrefix(), '/');
    		$backend = $this->getClass($resource,$set) or abort(404);
    		try {
	    		return $backend->execute($command, $id, $request);
	    	} catch (\Exception $e) {
	    		return response()->json('error', $e->getMessage() );
	    	}
    }

    /**
     * Reload tables, table rows and fields
     *
     * @return \Illuminate\Http\Response
     */
    public function reloadRouter(Request $request, $backend, $resource)
    {
    	$backend = $this->backend($backend);
//    	try {
	    	return $backend->reload($resource, $request);
//	    } catch (\Exception $e) {
//	    	abort(404);
//	    }
    }

    /**
     * Switch lookup request
     *
     * @return \Illuminate\Http\Response
     */
    public function lookupRouter(Request $request, $backend, $resource, $field)
    {
    	$backend = $this->backend($backend);
//    	try {
	    	return $backend->lookup($resource, $field, $request);
//	    } catch (\Exception $e) {
//	    	abort(404);
//	    }
    }

	private function getClass($class, $set)
	{
		$classPath = config('backend.namespace').'\\'.ucfirst(camel_case($class)).'Backend';
		if (class_exists($classPath)) {
			$backend = new $classPath($set);
			return $backend;
		}
		else return false;
	}
    
}
