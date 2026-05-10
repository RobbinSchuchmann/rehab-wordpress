import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, SelectControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

function PhasePanel( { phase, isActive } ) {
	const paragraphs = ( phase.paragraphs || [] ).filter( Boolean );
	const items = ( phase.listItems || [] ).filter( Boolean );
	return (
		<div
			className="rehab-treatment-phases__panel"
			data-label={ phase.label || '' }
			data-phase={ phase.phase || '' }
			hidden={ ! isActive }
		>
			<div className="rehab-treatment-phases__main">
				{ phase.h3 ? <h3>{ phase.h3 }</h3> : null }
				{ paragraphs.map( ( p, i ) => {
					if ( i === 1 && items.length ) {
						return (
							<>
								<p key={ `p-${ i }` }>{ p }</p>
								<ul key={ `ul-${ i }` }>
									{ items.map( ( it, j ) => <li key={ j } dangerouslySetInnerHTML={ { __html: it } } /> ) }
								</ul>
							</>
						);
					}
					return <p key={ i }>{ p }</p>;
				} ) }
			</div>
			{ phase.asideQuote ? (
				<aside className="rehab-treatment-phases__aside">
					<p className="quote">{ phase.asideQuote }</p>
					<div className="meta">
						<strong>{ phase.asideMetaLabel || 'Quoted by' }</strong>
						{ phase.asideMetaValue || '' }
					</div>
				</aside>
			) : null }
		</div>
	);
}

function Markup( { a } ) {
	const phases = Array.isArray( a.phases ) ? a.phases : [];
	return (
		<section className={ `rehab-treatment-phases rehab-bg-${ a.background }` }>
			<div className="rehab-container">
				{ ( a.eyebrow || a.heading || a.subheading ) ? (
					<div className="rehab-treatment-phases__head">
						{ a.eyebrow ? <span className="rehab-treatment-phases__eyebrow">{ a.eyebrow }</span> : null }
						{ a.heading ? <h2 className="rehab-treatment-phases__heading">{ a.heading }</h2> : null }
						{ a.subheading ? <p className="rehab-treatment-phases__sub">{ a.subheading }</p> : null }
					</div>
				) : null }
				<div className="rehab-treatment-phases__nav" role="tablist">
					{ phases.map( ( phase, i ) => (
						<button
							key={ i }
							type="button"
							role="tab"
							className={ `rehab-treatment-phases__tab${ i === 0 ? ' is-active' : '' }` }
							data-tab={ i }
							aria-selected={ i === 0 ? 'true' : 'false' }
						>
							<span>
								{ phase.phase ? <span className="num">{ phase.phase }</span> : null }
								<span>{ phase.label || `Phase ${ i + 1 }` }</span>
							</span>
						</button>
					) ) }
				</div>
				<div className="rehab-treatment-phases__panels">
					{ phases.map( ( phase, i ) => <PhasePanel key={ i } phase={ phase } isActive={ i === 0 } /> ) }
				</div>
			</div>
		</section>
	);
}

const ListEditor = ( { items, onChange, label = 'Items' } ) => {
	const update = ( i, v ) => { const next = items.slice(); next[ i ] = v; onChange( next ); };
	const remove = ( i ) => onChange( items.filter( ( _, j ) => j !== i ) );
	const add = () => onChange( [ ...items, '' ] );
	return (
		<div>
			<p style={ { fontWeight: 500, margin: '0.5rem 0 0.25rem' } }>{ label }</p>
			{ items.map( ( item, i ) => (
				<div key={ i } style={ { display: 'flex', gap: '0.5rem', marginBottom: '0.25rem' } }>
					<TextControl __next40pxDefaultSize value={ item } onChange={ ( v ) => update( i, v ) } style={ { flex: 1 } } />
					<Button variant="link" isDestructive onClick={ () => remove( i ) }>×</Button>
				</div>
			) ) }
			<Button variant="secondary" onClick={ add }>Add item</Button>
		</div>
	);
};

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const phases = Array.isArray( a.phases ) ? a.phases : [];
		const updatePhase = ( i, key, value ) => {
			const next = phases.slice();
			next[ i ] = { ...next[ i ], [ key ]: value };
			setAttributes( { phases: next } );
		};
		const addPhase = () => setAttributes( { phases: [ ...phases, { phase: '', label: '', h3: '', paragraphs: [], listItems: [], asideQuote: '', asideMetaLabel: 'Quoted by', asideMetaValue: '' } ] } );
		const removePhase = ( i ) => setAttributes( { phases: phases.filter( ( _, j ) => j !== i ) } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Section header" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' }, { label: 'Sage mist', value: 'sage-mist' } ] } onChange={ ( v ) => setAttributes( { background: v } ) } />
						<TextControl label="Eyebrow" value={ a.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } />
						<TextControl label="Heading" value={ a.heading } onChange={ ( v ) => setAttributes( { heading: v } ) } />
						<TextareaControl label="Subheading" value={ a.subheading } onChange={ ( v ) => setAttributes( { subheading: v } ) } />
					</PanelBody>
					{ phases.map( ( phase, i ) => (
						<PanelBody key={ i } title={ `Phase ${ i + 1 }${ phase.label ? ': ' + phase.label : '' }` } initialOpen={ false }>
							<TextControl label="Phase number / eyebrow" value={ phase.phase } onChange={ ( v ) => updatePhase( i, 'phase', v ) } help="e.g. PHASE 01" />
							<TextControl label="Label" value={ phase.label } onChange={ ( v ) => updatePhase( i, 'label', v ) } />
							<TextControl label="H3 heading" value={ phase.h3 } onChange={ ( v ) => updatePhase( i, 'h3', v ) } />
							<ListEditor label="Paragraphs" items={ phase.paragraphs || [] } onChange={ ( v ) => updatePhase( i, 'paragraphs', v ) } />
							<ListEditor label="Bullet list (optional, inserted between para 2 and 3)" items={ phase.listItems || [] } onChange={ ( v ) => updatePhase( i, 'listItems', v ) } />
							<TextareaControl label="Aside quote" value={ phase.asideQuote } onChange={ ( v ) => updatePhase( i, 'asideQuote', v ) } />
							<TextControl label="Aside meta label" value={ phase.asideMetaLabel } onChange={ ( v ) => updatePhase( i, 'asideMetaLabel', v ) } />
							<TextControl label="Aside meta value" value={ phase.asideMetaValue } onChange={ ( v ) => updatePhase( i, 'asideMetaValue', v ) } />
							<Button variant="link" isDestructive onClick={ () => removePhase( i ) }>Remove phase</Button>
						</PanelBody>
					) ) }
					<PanelBody title="" initialOpen>
						<Button variant="primary" onClick={ addPhase }>Add phase</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<Markup a={ a } />
				</div>
			</>
		);
	},
	save( { attributes } ) {
		return <Markup a={ attributes } />;
	},
} );
