/**
 * This class is generated by jOOQ
 */
package sooth.entities.tables.records;

/**
 * This class is generated by jOOQ.
 */
@javax.annotation.Generated(
	value = {
		"http://www.jooq.org",
		"jOOQ version:3.5.0"
	},
	comments = "This class is generated by jOOQ"
)
@java.lang.SuppressWarnings({ "all", "unchecked", "rawtypes" })
public class ProblemsRecord extends org.jooq.impl.UpdatableRecordImpl<sooth.entities.tables.records.ProblemsRecord> implements org.jooq.Record7<java.lang.Integer, java.lang.String, java.lang.String, java.lang.Integer, java.lang.String, java.lang.Integer, java.lang.Byte> {

	private static final long serialVersionUID = 1998669171;

	/**
	 * Setter for <code>asmregen.problems.id</code>.
	 */
	public void setId(java.lang.Integer value) {
		setValue(0, value);
	}

	/**
	 * Getter for <code>asmregen.problems.id</code>.
	 */
	public java.lang.Integer getId() {
		return (java.lang.Integer) getValue(0);
	}

	/**
	 * Setter for <code>asmregen.problems.name</code>.
	 */
	public void setName(java.lang.String value) {
		setValue(1, value);
	}

	/**
	 * Getter for <code>asmregen.problems.name</code>.
	 */
	public java.lang.String getName() {
		return (java.lang.String) getValue(1);
	}

	/**
	 * Setter for <code>asmregen.problems.description</code>.
	 */
	public void setDescription(java.lang.String value) {
		setValue(2, value);
	}

	/**
	 * Getter for <code>asmregen.problems.description</code>.
	 */
	public java.lang.String getDescription() {
		return (java.lang.String) getValue(2);
	}

	/**
	 * Setter for <code>asmregen.problems.pluginId</code>.
	 */
	public void setPluginid(java.lang.Integer value) {
		setValue(3, value);
	}

	/**
	 * Getter for <code>asmregen.problems.pluginId</code>.
	 */
	public java.lang.Integer getPluginid() {
		return (java.lang.Integer) getValue(3);
	}

	/**
	 * Setter for <code>asmregen.problems.config</code>.
	 */
	public void setConfig(java.lang.String value) {
		setValue(4, value);
	}

	/**
	 * Getter for <code>asmregen.problems.config</code>.
	 */
	public java.lang.String getConfig() {
		return (java.lang.String) getValue(4);
	}

	/**
	 * Setter for <code>asmregen.problems.lectureId</code>.
	 */
	public void setLectureid(java.lang.Integer value) {
		setValue(5, value);
	}

	/**
	 * Getter for <code>asmregen.problems.lectureId</code>.
	 */
	public java.lang.Integer getLectureid() {
		return (java.lang.Integer) getValue(5);
	}

	/**
	 * Setter for <code>asmregen.problems.deleted</code>.
	 */
	public void setDeleted(java.lang.Byte value) {
		setValue(6, value);
	}

	/**
	 * Getter for <code>asmregen.problems.deleted</code>.
	 */
	public java.lang.Byte getDeleted() {
		return (java.lang.Byte) getValue(6);
	}

	// -------------------------------------------------------------------------
	// Primary key information
	// -------------------------------------------------------------------------

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Record1<java.lang.Integer> key() {
		return (org.jooq.Record1) super.key();
	}

	// -------------------------------------------------------------------------
	// Record7 type implementation
	// -------------------------------------------------------------------------

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Row7<java.lang.Integer, java.lang.String, java.lang.String, java.lang.Integer, java.lang.String, java.lang.Integer, java.lang.Byte> fieldsRow() {
		return (org.jooq.Row7) super.fieldsRow();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Row7<java.lang.Integer, java.lang.String, java.lang.String, java.lang.Integer, java.lang.String, java.lang.Integer, java.lang.Byte> valuesRow() {
		return (org.jooq.Row7) super.valuesRow();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Integer> field1() {
		return sooth.entities.tables.Problems.PROBLEMS.ID;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field2() {
		return sooth.entities.tables.Problems.PROBLEMS.NAME;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field3() {
		return sooth.entities.tables.Problems.PROBLEMS.DESCRIPTION;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Integer> field4() {
		return sooth.entities.tables.Problems.PROBLEMS.PLUGINID;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field5() {
		return sooth.entities.tables.Problems.PROBLEMS.CONFIG;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Integer> field6() {
		return sooth.entities.tables.Problems.PROBLEMS.LECTUREID;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Byte> field7() {
		return sooth.entities.tables.Problems.PROBLEMS.DELETED;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Integer value1() {
		return getId();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value2() {
		return getName();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value3() {
		return getDescription();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Integer value4() {
		return getPluginid();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value5() {
		return getConfig();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Integer value6() {
		return getLectureid();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Byte value7() {
		return getDeleted();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ProblemsRecord value1(java.lang.Integer value) {
		setId(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ProblemsRecord value2(java.lang.String value) {
		setName(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ProblemsRecord value3(java.lang.String value) {
		setDescription(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ProblemsRecord value4(java.lang.Integer value) {
		setPluginid(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ProblemsRecord value5(java.lang.String value) {
		setConfig(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ProblemsRecord value6(java.lang.Integer value) {
		setLectureid(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ProblemsRecord value7(java.lang.Byte value) {
		setDeleted(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ProblemsRecord values(java.lang.Integer value1, java.lang.String value2, java.lang.String value3, java.lang.Integer value4, java.lang.String value5, java.lang.Integer value6, java.lang.Byte value7) {
		return this;
	}

	// -------------------------------------------------------------------------
	// Constructors
	// -------------------------------------------------------------------------

	/**
	 * Create a detached ProblemsRecord
	 */
	public ProblemsRecord() {
		super(sooth.entities.tables.Problems.PROBLEMS);
	}

	/**
	 * Create a detached, initialised ProblemsRecord
	 */
	public ProblemsRecord(java.lang.Integer id, java.lang.String name, java.lang.String description, java.lang.Integer pluginid, java.lang.String config, java.lang.Integer lectureid, java.lang.Byte deleted) {
		super(sooth.entities.tables.Problems.PROBLEMS);

		setValue(0, id);
		setValue(1, name);
		setValue(2, description);
		setValue(3, pluginid);
		setValue(4, config);
		setValue(5, lectureid);
		setValue(6, deleted);
	}
}