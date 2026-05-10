import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

function Edit( { attributes, setAttributes } ) {
	const { background, heading, leftLabel, rightLabel, rows } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-comparison rehab-bg-${ background }`,
	} );

	const updateRow = ( idx, key, value ) => {
		const next = rows.map( ( r, i ) => ( i === idx ? { ...r, [ key ]: value } : r ) );
		setAttributes( { rows: next } );
	};

	const addRow = () =>
		setAttributes( {
			rows: [ ...rows, { topic: 'New row', left: '', right: '' } ],
		} );

	const removeRow = ( idx ) =>
		setAttributes( { rows: rows.filter( ( _, i ) => i !== idx ) } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Comparison settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Background', 'rehab-blocks' ) }
						value={ background }
						options={ [
							{ label: 'White', value: 'white' },
							{ label: 'Cream', value: 'cream' },
							{ label: 'Sage mist', value: 'sage-mist' },
						] }
						onChange={ ( v ) => setAttributes( { background: v } ) }
					/>
					<Button variant="secondary" onClick={ addRow } style={ { marginTop: '1rem' } }>
						{ __( '+ Add row', 'rehab-blocks' ) }
					</Button>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="rehab-container rehab-container--narrow">
					<header className="rehab-comparison__header">
						<RichText
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
							placeholder={ __( 'Heading…', 'rehab-blocks' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
					</header>
					<div className="rehab-comparison__grid">
						<div className="rehab-comparison__col rehab-comparison__col--header">
							<span className="rehab-comparison__topic-label" />
						</div>
						<div className="rehab-comparison__col rehab-comparison__col--header rehab-comparison__col--ours">
							<RichText
								tagName="span"
								value={ leftLabel }
								onChange={ ( v ) => setAttributes( { leftLabel: v } ) }
								placeholder={ __( 'Our column label', 'rehab-blocks' ) }
								allowedFormats={ [] }
							/>
						</div>
						<div className="rehab-comparison__col rehab-comparison__col--header">
							<RichText
								tagName="span"
								value={ rightLabel }
								onChange={ ( v ) => setAttributes( { rightLabel: v } ) }
								placeholder={ __( 'Their column label', 'rehab-blocks' ) }
								allowedFormats={ [] }
							/>
						</div>
						{ rows.map( ( row, idx ) => (
							<>
								<div className="rehab-comparison__cell rehab-comparison__cell--topic" key={ `t-${ idx }` }>
									<RichText
										tagName="span"
										value={ row.topic }
										onChange={ ( v ) => updateRow( idx, 'topic', v ) }
										placeholder={ __( 'Topic', 'rehab-blocks' ) }
										allowedFormats={ [] }
									/>
								</div>
								<div className="rehab-comparison__cell rehab-comparison__cell--ours" key={ `l-${ idx }` }>
									<RichText
										tagName="span"
										value={ row.left }
										onChange={ ( v ) => updateRow( idx, 'left', v ) }
										placeholder={ __( 'Our value', 'rehab-blocks' ) }
									/>
								</div>
								<div className="rehab-comparison__cell" key={ `r-${ idx }` }>
									<RichText
										tagName="span"
										value={ row.right }
										onChange={ ( v ) => updateRow( idx, 'right', v ) }
										placeholder={ __( 'Their value', 'rehab-blocks' ) }
									/>
									<button
										type="button"
										className="rehab-comparison__remove"
										onClick={ () => removeRow( idx ) }
										aria-label={ __( 'Remove row', 'rehab-blocks' ) }
									>
										×
									</button>
								</div>
							</>
						) ) }
					</div>
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, heading, leftLabel, rightLabel, rows } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-comparison rehab-bg-${ background }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container rehab-container--narrow">
				{ heading && (
					<header className="rehab-comparison__header">
						<RichText.Content
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
						/>
					</header>
				) }
				<div className="rehab-comparison__grid">
					<div className="rehab-comparison__col rehab-comparison__col--header" />
					<div className="rehab-comparison__col rehab-comparison__col--header rehab-comparison__col--ours">
						<RichText.Content tagName="span" value={ leftLabel } />
					</div>
					<div className="rehab-comparison__col rehab-comparison__col--header">
						<RichText.Content tagName="span" value={ rightLabel } />
					</div>
					{ rows.map( ( row, idx ) => {
						const isLast = idx === rows.length - 1;
						const oursClass = `rehab-comparison__cell rehab-comparison__cell--ours${ isLast ? ' rehab-comparison__cell--last' : '' }`;
						return (
							<>
								<div className="rehab-comparison__cell rehab-comparison__cell--topic" key={ `t-${ idx }` }>
									<RichText.Content tagName="span" value={ row.topic } />
								</div>
								<div className={ oursClass } key={ `l-${ idx }` }>
									<RichText.Content tagName="span" value={ row.left } />
								</div>
								<div className="rehab-comparison__cell" key={ `r-${ idx }` }>
									<RichText.Content tagName="span" value={ row.right } />
								</div>
							</>
						);
					} ) }
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
