////////////////////////////////////////////////////////
//
// GEM - Graphics Environment for Multimedia
//
// zmoelnig@iem.kug.ac.at
//
// Implementation file 
//
//    Copyright (c) 1997-1999 Mark Danks.
//    Copyright (c) Günther Geiger.
//    Copyright (c) 2001-2011 IOhannes m zmoelnig. forum::für::umläute. IEM
//    For information on usage and redistribution, and for a DISCLAIMER OF ALL
//    WARRANTIES, see the file, "GEM.LICENSE.TERMS" in this distribution.
//
/////////////////////////////////////////////////////////
#ifdef HAVE_CONFIG_H
# include "config.h"
#endif

#include <string.h>
#include "imageSGI.h"
#include "Gem/RTE.h"
#include "imageSGI.h"
#include "sgiimage.h"


using namespace gem::plugins;

REGISTER_IMAGELOADERFACTORY("SGI", imageSGI);


/////////////////////////////////////////////////////////
//
// imageSGI
//
/////////////////////////////////////////////////////////
// Constructor
//
/////////////////////////////////////////////////////////
imageSGI :: imageSGI() 
{
  //post("imageSGI");
}
imageSGI :: ~imageSGI()
{
  //post("~imageSGI");
}

/////////////////////////////////////////////////////////
// really open the file ! (OS dependent)
//
/////////////////////////////////////////////////////////
bool imageSGI :: load(std::string filename, imageStruct&result, gem::Properties&props)
{
  int32 xsize, ysize, csize;
  if (!sizeofimage(filename.c_str(), &xsize, &ysize, &csize) )
    return(NULL);

  ::verbose(2, "reading '%s' with SGI", filename.c_str());

  result.xsize=xsize;
  result.ysize=ysize;

  if (csize == 4 || csize == 3) {
    result.setCsizeByFormat(GL_RGBA);
  } else if (csize == 1) {
    result.setCsizeByFormat(GL_LUMINANCE);
  } else {
    //error("GemImageLoad(SGI): unknown color components in SGI file: %s", filename.c_str());
    return(false);
  }
  result.reallocate();
   
  unsigned int32 *readData = longimagedata((char *)filename.c_str());
  if (!readData) {
    //error("GemImageLoad: error reading SGI image file: %s", filename.c_str());
    return false;
  }

  unsigned char *src = reinterpret_cast<unsigned char*>(readData);
  unsigned char *dst = &(result.data[0]);
  const int yStride = result.xsize * result.csize;

  // do RGBA data
  if (csize == 4) {
    while (ysize--) {
      unsigned char *pixels = dst;
      int count = xsize;
      while(count--) {
	pixels[chRed]   = src[0];
	pixels[chGreen] = src[1];
	pixels[chBlue]  = src[2];
	pixels[chAlpha] = src[3];
	pixels += 4;
	src += 4;
      }
      dst += yStride;
    }
  }
  else if (csize == 3) {
    // do RGB data
    while (ysize--) {
      unsigned char *pixels = dst;
      int count = xsize;
      while(count--) {
	pixels[chRed]   = src[0];
	pixels[chGreen] = src[1];
	pixels[chBlue]  = src[2];
	pixels[chAlpha] = 255;;
	pixels += 4;
	src += 4;
      }
      dst += yStride;
    }
  } else  {
    // do grayscale
    while (ysize--) {
      unsigned char *pixels = dst;
      int count = xsize;
      while(count--) {
	pixels[0] = src[0];
	pixels++;
	src += 4;
      }
      dst += yStride;
    }
  }

  free(readData);
   
  return true;
}
bool imageSGI::save(std::string filename, const imageStruct&constimage) {
  return false;
}
