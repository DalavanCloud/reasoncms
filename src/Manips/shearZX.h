/*-----------------------------------------------------------------
LOG
    GEM - Graphics Environment for Multimedia

    shear a gem object

    Copyright (c) 1997-1999 Mark Danks. mark@danks.org
    Copyright (c) Günther Geiger. geiger@epy.co.at
    Copyright (c) 2001-2002 IOhannes m zmoelnig. forum::für::umläute. IEM. zmoelnig@iem.kug.ac.at
    For information on usage and redistribution, and for a DISCLAIMER OF ALL
    WARRANTIES, see the file, "GEM.LICENSE.TERMS" in this distribution.

-----------------------------------------------------------------*/


#ifndef INCLUDE_shearZX_H_
#define INCLUDE_shearZX_H_

#include "Base/GemBase.h"

/*-----------------------------------------------------------------
-------------------------------------------------------------------
CLASS
    shearZX
    
    shear a gem object

DESCRIPTION
    
  

-----------------------------------------------------------------*/
class GEM_EXTERN shearZX : public GemBase
{
    CPPEXTERN_HEADER(shearZX, GemBase);

    public:

        //////////
        // Constructor
    	shearZX(int argc, t_atom *argv);
    	
    protected:
    	
    	//////////
    	// Destructor
    	virtual ~shearZX();

    	//////////
    	// When rendering occurs
    	virtual void	render(GemState *state);


    	//////////
    	// X value changed
    	void	    	shearMess(float val);

		//shear value
		float			shear;
    	
    private:
    	
    	//////////
    	// static member functions
    	static void 	shearMessCallback(void *data, t_floatarg val);

};

#endif	// for header file