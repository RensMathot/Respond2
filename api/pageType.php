<?php

/**
 * A protected API call to add a pagetype
 * @uri /pagetype/add
 */
class PageTypeAddResource extends Tonic\Resource {

    /**
     * @method POST
     */
    function add() {

        // get an authuser
        $authUser = new AuthUser();

        if(isset($authUser->UserUniqId)){ // check if authorized

            parse_str($this->request->data, $request); // parse request

            $friendlyId = $request['friendlyId'];
            $typeS = $request['typeS'];
            $typeP = $request['typeP'];
            $layout = $request['layout'];
            $stylesheet = $request['stylesheet'];
            $siteId = $authUser->SiteId;
            $createdBy = $authUser->UserId;
            $lastModifiedBy = $authUser->UserId;

            $pageType = PageType::Add($friendlyId, $typeS, $typeP, $layout, $stylesheet, $siteId, $createdBy, $lastModifiedBy);

            // return a json response
            $response = new Tonic\Response(Tonic\Response::OK);
            $response->contentType = 'applicaton/json';
            $response->body = json_encode($pageType);

            return $response;
        
        } else{ // unauthorized access

            return new Tonic\Response(Tonic\Response::UNAUTHORIZED);
        }
    }

}

/**
 * A protected API call to add a pagetype
 * @uri /pagetype/edit
 */
class PageTypeEditResource extends Tonic\Resource {

    /**
     * @method POST
     */
    function edit() {

        // get an authuser
        $authUser = new AuthUser();

        if(isset($authUser->UserUniqId)){ // check if authorized

            parse_str($this->request->data, $request); // parse request

			$pageTypeUniqId = $request['pageTypeUniqId'];
            $typeS = $request['typeS'];
            $typeP = $request['typeP'];
            $layout = $request['layout'];
            $stylesheet = $request['stylesheet'];
            $lastModifiedBy = $authUser->UserId;
            
            PageType::Edit($pageTypeUniqId, $typeS, $typeP, $layout, $stylesheet, $lastModifiedBy);

            // return a json response
            return new Tonic\Response(Tonic\Response::OK);
        
        } else{ // unauthorized access

            return new Tonic\Response(Tonic\Response::UNAUTHORIZED);
        }
    }

}

/**
 * A protected API call to update and remove a page type
 * @uri /pagetype/{pageTypeUniqId}
 */
class PageTypeResource extends Tonic\Resource {

    /**
     * @method DELETE
     */
    function delete($pageTypeUniqId) {
        // get an authuser
        $authUser = new AuthUser();

        if(isset($authUser->UserUniqId)){ // check if authorized
        
        	$pageType = PageType::GetByPageTypeUniqId($pageTypeUniqId);
			$site = Site::GetBySiteId($pageType['SiteId']);
			
			// remove pages for that pagetype in that site
			$dir = '../sites/'.$site['FriendlyId'].'/'.$pageType['FriendlyId'];
			
			if(file_exists($dir)){
				Utilities::RemoveDirectory($dir);
			}
		
			// remove page type and pages from DB
            PageType::Delete($pageType['PageTypeId']);

            return new Tonic\Response(Tonic\Response::OK);
        }
        else{
            return new Tonic\Response(Tonic\Response::UNAUTHORIZED);
        }
    }

}

/**
 * This class defines an example resource that is wired into the URI /example
 * @uri /pagetype/list/all
 */
class PageTypeListAllResource extends Tonic\Resource {

    /**
     * @method GET
     */
    function get() {

        // get an authuser
        $authUser = new AuthUser();

        if(isset($authUser->UserUniqId)){ // check if authorized

            $siteId = $authUser->SiteId;

            // get pagetype
            $list = PageType::GetPageTypes($siteId);

            // return a json response
            $response = new Tonic\Response(Tonic\Response::OK);
            $response->contentType = 'applicaton/json';
            $response->body = json_encode($list);

            return $response;

        }
        else{ // unauthorized access

            return new Tonic\Response(Tonic\Response::UNAUTHORIZED);
        }

    }

}

?>