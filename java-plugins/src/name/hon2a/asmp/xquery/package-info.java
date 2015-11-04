/**
 * This plugin checks students' submissions for the XQuery homework (sixth homework) for correctness.
 *
 * The plugin uses classes from the name.hon2a.asm package.
 * The plugin launches the test XqueryTest which tests the given data.xml file for well-formedness and then runs, in order,
 * all the query files given. It stores the result of each query in a file (these files are then made available to both the
 * student and the teacher). It also check the query files for the use of constructs required by the assignment.
 *
 * See the thesis by Petr Hudeček for full description of the tasks given to the students.
 */
package name.hon2a.asmp.xquery;