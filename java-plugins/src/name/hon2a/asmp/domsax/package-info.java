/**
 * This plugin checks students' submissions for the DOM/SAX homework (second homework) for correctness.
 *
 * The plugin uses classes from the name.hon2a.asm package.
 * The plugin launches, in order, the tests DomJavaTest and SaxJavaTest. Each of these tests first uses the bundled
 * JAR file "tools.jar" to compile the source code given by the student, then takes control of output and error streams,
 * then runs the executable code by the students and creates output, then outputs XML data as specified by XML Check's
 * plugin contract (see the thesis of Jan Konopasek for this documentation).
 *
 * See the thesis by Petr Hudeček for full description of the tasks given to the students.
 */
package name.hon2a.asmp.domsax;