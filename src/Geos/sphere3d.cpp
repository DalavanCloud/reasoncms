////////////////////////////////////////////////////////
//
// GEM - Graphics Environment for Multimedia
//
// zmoelnig@iem.kug.ac.at
//
// Implementation file
//
//    Copyright (c) 1997-1998 Mark Danks.
//    Copyright (c) G�nther Geiger.
//    Copyright (c) 2001-2002 IOhannes m zmoelnig. forum::f�r::uml�ute. IEM
//    For information on usage and redistribution, and for a DISCLAIMER OF ALL
//    WARRANTIES, see the file, "GEM.LICENSE.TERMS" in this distribution.
//
/////////////////////////////////////////////////////////

#include "sphere3d.h"

CPPEXTERN_NEW_WITH_THREE_ARGS(sphere3d, t_floatarg, A_DEFFLOAT, t_floatarg, A_DEFFLOAT, t_floatarg, A_DEFFLOAT)

/////////////////////////////////////////////////////////
//
// sphere3d
//
/////////////////////////////////////////////////////////
// Constructor
//
/////////////////////////////////////////////////////////
sphere3d :: sphere3d(t_floatarg size, t_floatarg slize, t_floatarg stack)
  : GemGluObj(size, slize, stack), 
    m_x(NULL), m_y(NULL), m_z(NULL),
    oldStacks(-1), oldSlices(-1), oldDrawType(0), oldTexture(-1),
    m_displayList(0)
{
}

////////////////////////////////////////////////////////
// Destructor
//
/////////////////////////////////////////////////////////
sphere3d :: ~sphere3d()
{ }

void sphere3d :: createSphere3d()
{
  // GLdouble radius=m_size;
  GLint slices=m_numSlices;
  GLint stacks=m_numStacks;

  GLfloat rho=90.f, drho=0.f, theta=0.f, dtheta=0.f;
  GLint i, j;

  //  post("creating sphere %d %d", slices, stacks);

  if(m_drawType==GL_DEFAULT_GEM)m_drawType=GL_FILL;

  drho = 180 / (GLfloat) stacks;
  dtheta = 360 / (GLfloat) slices;

  delete[]m_x;m_x = new float[slices * (stacks-1) + 2];
  delete[]m_y;m_y = new float[slices * (stacks-1) + 2];
  delete[]m_z;m_z = new float[slices * (stacks-1) + 2];

  int src=0;

  m_x[src] = 0.f;
  m_y[src] = 0.f;
  m_z[src] = 1.f;
  src++;
  
  rho=90;
  for(i=0; i<stacks-1; i++){
    rho-=drho;
    theta=0.f;
    for (j = 0; j < slices; j++) {
      theta+=dtheta;
      
      m_x[src] = cos(theta*M_PI/180) * cos(rho*M_PI/180);
      m_y[src] = sin(theta*M_PI/180) * cos(rho*M_PI/180);
      m_z[src] = sin(rho  *M_PI/180);

      src++;
    }
  }

  m_x[src] = 0.f;
  m_y[src] = 0.f;
  m_z[src] = -1.f;
  src++;

  setModified();
}
/////////////////////////////////////////////////////////
// render
//
/////////////////////////////////////////////////////////
void sphere3d :: setMess(int i, int j,
                         GLfloat x, GLfloat y, GLfloat z)
{
  int index=0;
  if(i<0||i>=m_numSlices){
    error("slice-index must be within 0..%d", m_numSlices-1);
    return;
  }
  if(j<0||j>m_numStacks){
    error("stack-index must be within 0..%d", m_numStacks);
    return;
  }

  if(j==0) index=0;
  else if (j==m_numStacks) index=(m_numSlices*(m_numStacks-1)+1);
  else index=(m_numSlices)*(j-1)+i+1;

  m_x[index]=x;
  m_y[index]=y;
  m_z[index]=z;

  setModified();
}
/////////////////////////////////////////////////////////
// render
//
/////////////////////////////////////////////////////////
void sphere3d :: render(GemState *state)
{
  GLint slices=m_numSlices;
  GLint stacks=m_numStacks;

  GLfloat s, t, ds, dt;
  GLint i, j;
  int src=0;
  
  glPushMatrix();
  glScalef(m_size, m_size, m_size);

  /* i cannot remember right now why we don't want normals always to be build
   * if lighting is off, they just won't be used
   * i guess the original reason was somehow related to performance 
   *
   * GLboolean normals = (state->lighting)?GL_TRUE:GL_FALSE;
   */
  GLboolean normals = GL_TRUE;
  GLint texture = (state->texture);

  GLfloat xsize = 1.0, xsize0 = 0.0;
  GLfloat ysize = 1.0, ysize0 = 0.0;

  if(texture && state->numTexCoords>=3){
    xsize0 = state->texCoords[0].s;
    xsize  = state->texCoords[1].s-xsize0;
    ysize0 = state->texCoords[1].t;
    ysize  = state->texCoords[2].t-ysize0;
  }
    
  /* texturing: s goes from 0.0/0.25/0.5/0.75/1.0 at +y/+x/-y/-x/+y axis */
  /* t goes from -1.0/+1.0 at z = -radius/+radius (linear along longitudes) */
  /* cannot use triangle fan on texturing (s coord. at top/bottom tip varies) */

  //if anything changed then the geometry is rebuilt
  if (stacks != oldStacks || slices != oldSlices){
    //call the sphere3d creation function to fill the array
    createSphere3d();
    
    oldStacks = stacks;
    oldSlices = slices;
  }
  if (m_drawType != oldDrawType || texture!=oldTexture)m_modified=true;
  oldDrawType = m_drawType;
  oldTexture = texture;
  
  if(!m_displayList)m_modified=true;
  
  if(m_modified){

    if(m_displayList)glDeleteLists(m_displayList, 1);

    m_displayList=glGenLists(1);
    glNewList(m_displayList, GL_COMPILE_AND_EXECUTE);
 
    if (m_drawType == GL_FILL) {
      t = 1.0;
      s = 0.0;
      ds = 1.0 / slices;
      dt = 1.0 / stacks;

      /* draw +Z end as a quad strip */
      glBegin(GL_QUAD_STRIP);

      src=1;
      for (j = 0; j < slices; j++) {
        if(normals)glNormal3f(m_x[0], m_y[0], m_z[0]);
        if(texture)glTexCoord2f(s*xsize+xsize0, t*ysize+ysize0);
        glVertex3f(m_x[0], m_y[0], m_z[0]);
    
        if(normals)glNormal3f(m_x[src], m_y[src], m_z[src]);
        if(texture)glTexCoord2f(s*xsize+xsize0, (t-dt)*ysize+ysize0);
        glVertex3f(m_x[src], m_y[src], m_z[src]);

        src++;
        s += ds;
      }
      src=1;
      if(normals)glNormal3f(m_x[0], m_y[0], m_z[0]);
      if(texture)glTexCoord2f(1.f*xsize+xsize0, t*ysize+ysize0);
      glVertex3f(m_x[0], m_y[0], m_z[0]);
    
      if(normals)glNormal3f(m_x[src], m_y[src], m_z[src]);
      if(texture)glTexCoord2f(1.f*xsize+xsize0, (t-dt)*ysize+ysize0);
      glVertex3f(m_x[src], m_y[src], m_z[src]);

      glEnd();
      t-=dt;

      /* draw intermediate stacks as quad strips */
      src=1;
      for (i = 0; i < stacks-2; i++) {
        int src2=0;
        s = 0.0;
        glBegin(GL_QUAD_STRIP);
        for (j = 0; j < slices; j++) {
          src2=src+slices;

          if(normals)glNormal3f(m_x[src], m_y[src], m_z[src]);
          if(texture)glTexCoord2f(s*xsize+xsize0, t*ysize+ysize0);
          glVertex3f(m_x[src], m_y[src], m_z[src]);
          src++;

          if(normals)glNormal3f(m_x[src2], m_y[src2], m_z[src2]);
          if(texture)glTexCoord2f(s*xsize+xsize0, (t - dt)*ysize+ysize0);
          glVertex3f(m_x[src2], m_y[src2], m_z[src2]);
          src2++;

          s += ds;
        }

        if(normals)glNormal3f(m_x[src-slices], m_y[src-slices], m_z[src-slices]);
        if(texture)glTexCoord2f(s*xsize+xsize0, t*ysize+ysize0);
        glVertex3f(m_x[src-slices], m_y[src-slices], m_z[src-slices]);

        if(normals)glNormal3f(m_x[src2-slices], m_y[src2-slices], m_z[src2-slices]);
        if(texture)glTexCoord2f(s*xsize+xsize0, (t - dt)*ysize+ysize0);
        glVertex3f(m_x[src2-slices], m_y[src2-slices], m_z[src2-slices]);

        glEnd();
        t -= dt;
      }

      /* draw -Z end as a quad strip */
      glBegin(GL_QUAD_STRIP);

      src=(slices*(stacks-2)+1);
      const int last=slices*(stacks-1)+1;
      s=0.0;
      for (j = 0; j < slices; j++) {
        if(normals)glNormal3f(m_x[src], m_y[src], m_z[src]);
        if(texture)glTexCoord2f(s*xsize+xsize0, t*ysize+ysize0);
        glVertex3f(m_x[src], m_y[src], m_z[src]);
        src++;

        if(normals)glNormal3f(m_x[last], m_y[last], m_z[last]);
        if(texture)glTexCoord2f(s*xsize+xsize0, (t-dt)*ysize+ysize0);
        glVertex3f(m_x[last], m_y[last], m_z[last]);

        s+=ds;
      }
      src=(slices*(stacks-2)+1);
      if(normals)glNormal3f(m_x[src], m_y[src], m_z[src]);
      if(texture)glTexCoord2f(1.f*xsize+xsize0, t*ysize+ysize0);
      glVertex3f(m_x[src], m_y[src], m_z[src]);

      if(normals)glNormal3f(m_x[last], m_y[last], m_z[last]);
      if(texture)glTexCoord2f(1.f*xsize+xsize0, (t-dt)*ysize+ysize0);
      glVertex3f(m_x[last], m_y[last], m_z[last]);

      glEnd();

    }
    else if (m_drawType == GL_LINE || m_drawType == GLU_SILHOUETTE) {
     
      src = 1;      
      for (i = 1; i < stacks; i++) {	// stack line at i==stacks-1 was missing here
        glBegin(GL_LINE_LOOP);
        for (j = 0; j < slices; j++) {
        
          if (normals)
            glNormal3f(m_x[src], m_y[src], m_z[src]);
          glVertex3f(m_x[src], m_y[src], m_z[src]);
          src++;
        }
        glEnd();
      }
      for (j = 0; j < slices; j++) {
        glBegin(GL_LINE_STRIP);

        if (normals)
          glNormal3f(m_x[0], m_y[0], m_z[0]);
        glVertex3f(m_x[0], m_y[0], m_z[0]);

        for (i = 0; i < stacks-1; i++) {
          src=i*slices+1+j;
          if (normals)
            glNormal3f(m_x[src], m_y[src], m_z[src]);
          glVertex3f(m_x[src], m_y[src], m_z[src]);
        }
        src=slices*(stacks-1)+1;
        if (normals)
          glNormal3f(m_x[src], m_y[src], m_z[src]);
        glVertex3f(m_x[src], m_y[src], m_z[src]);

        glEnd();
      }
    }
  
    else if (m_drawType == GL_POINT) {
      /* top and bottom-most points */
      src=0;

      glBegin(GL_POINTS);

      if(normals)
        glNormal3f(m_x[src], m_y[src], m_z[src]);
      glVertex3f(m_x[src], m_y[src], m_z[src]);
      src++;

      for (i = 0; i < stacks-1; i++) {
        for (j = 0; j < slices; j++) {
          if (normals)
            glNormal3f(m_x[src], m_y[src], m_z[src]);
          glVertex3f(m_x[src], m_y[src], m_z[src]);
          src++;
        }
      }
      if(normals)
        glNormal3f(m_x[src], m_y[src], m_z[src]);
      glVertex3f(m_x[src], m_y[src], m_z[src]);

      glEnd();
    }
    glEndList();
  } /* rebuild list */
  else glCallList(m_displayList);

  glPopMatrix();
  m_modified=false;
}
/////////////////////////////////////////////////////////
// static member function
//
/////////////////////////////////////////////////////////
void sphere3d :: obj_setupCallback(t_class *classPtr)
{
  class_addmethod(classPtr, (t_method)&sphere3d::setCartMessCallback, gensym("set"), 
                  A_FLOAT, A_FLOAT, A_FLOAT, A_FLOAT, A_FLOAT, A_NULL);
  class_addmethod(classPtr, (t_method)&sphere3d::setCartMessCallback, gensym("setCartesian"), 
                  A_FLOAT, A_FLOAT, A_FLOAT, A_FLOAT, A_FLOAT, A_NULL);
  class_addmethod(classPtr, (t_method)&sphere3d::setCartMessCallback, gensym("setSpherical"), 
                  A_FLOAT, A_FLOAT, A_FLOAT, A_FLOAT, A_FLOAT, A_NULL);
  class_addmethod(classPtr, (t_method)&sphere3d::setSphMessCallback, gensym("setSph"), 
                  A_FLOAT, A_FLOAT, A_FLOAT, A_FLOAT, A_FLOAT, A_NULL);
}

void sphere3d :: setCartMessCallback(void *data,
                                    t_floatarg i, t_floatarg j,
                                    t_floatarg x, t_floatarg y, t_floatarg z)
{
  GetMyClass(data)->setMess((int)i, (int)j, x, y, z);
}
void sphere3d :: setSphMessCallback(void *data,
                                    t_floatarg i, t_floatarg j,
                                    t_floatarg r, t_floatarg phi, t_floatarg theta)
{
  t_float x = r*cos(phi)*cos(theta);
  t_float y = r*sin(phi)*cos(theta);
  t_float z = r*sin(theta);

  GetMyClass(data)->setMess((int)i, (int)j, x, y, z);
}
