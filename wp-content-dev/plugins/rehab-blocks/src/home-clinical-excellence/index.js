import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const stats = a.stats || [];
		const blockProps = useBlockProps( { className: 'drt-clinical drt-bg-white' } );
		const setStat = ( i, key ) => ( v ) => {
			const next = stats.map( ( s, idx ) => ( idx === i ? { ...s, [ key ]: v } : s ) );
			setAttributes( { stats: next } );
		};
		return (
			<>
				<InspectorControls>
					<PanelBody title="Stats" initialOpen>
						{ stats.map( ( s, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Stat ${ i + 1 } value` } value={ s.value } onChange={ setStat( i, 'value' ) } />
								<TextControl label={ `Stat ${ i + 1 } label` } value={ s.label } onChange={ setStat( i, 'label' ) } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { stats: stats.filter( ( _, j ) => j !== i ) } ) }>Remove stat</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { stats: [ ...stats, { value: '', label: '' } ] } ) }>Add stat</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container drt-container--narrow">
						<div className="drt-clinical__grid">
							{ stats.map( ( s, i ) => (
								<div className={ `drt-clinical__stat${ i < stats.length - 1 ? ' drt-clinical__stat--bordered' : '' }` } key={ i }>
									<RichText tagName="span" className="drt-clinical__value" value={ s.value } onChange={ setStat( i, 'value' ) } placeholder="12+" allowedFormats={ [] } />
									<RichText tagName="span" className="drt-clinical__label" value={ s.label } onChange={ setStat( i, 'label' ) } placeholder="Label…" allowedFormats={ [] } />
								</div>
							) ) }
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
